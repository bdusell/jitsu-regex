<?php

/**
 * Regular expression utility functions.
 */

namespace Jitsu;

/**
 * A collection of static methods for dealing with regular expressions.
 */
class RegexUtil {

	/**
	 * Create a regular expression from a PCRE pattern.
	 *
	 * This converts a string containing a PCRE pattern to a value
	 * compatible for use with this module. Do not include the delimiters;
	 * they will be added and escaped automatically. You can specify any
	 * flags in a separate string. If you know that your PCRE is already
	 * properly escaped with respect to a certain set of deliters, you can
	 * optionally provide the start and ending delimiters to use and avoid
	 * the overhead of escaping the pattern.
	 *
	 * @param string $pat A PCRE pattern with *no* delimiters.
	 * @param string $flags Optional PCRE flags.
	 * @param string|null $start An optional start delimiter to use.
	 * @param string|null $end An optional end delimiter to use. Defaults
	 *                         to the start delimiter.
	 * @return string
	 */
	public static function create($pat, $flags = '', $start = null, $end = null) {
		if($start === null) {
			return '/' . str_replace('/', '\\/', $pat) . '/' . $flags;
		} else {
			if($end === null) $end = $start;
			return $start . $pat . $end . $flags;
		}
	}

	/**
	 * Get an error string for a `PREG_` error code.
	 *
	 * @param int $code A `PREG_` error code.
	 * @return string
	 */
	public static function errorString($code) {
		static $codes = array(
			PREG_NO_ERROR => 'no error',
			PREG_INTERNAL_ERROR => 'internal error',
			PREG_BACKTRACK_LIMIT_ERROR => 'backtrack limit error',
			PREG_RECURSION_LIMIT_ERROR => 'recursion limit error',
			PREG_BAD_UTF8_ERROR => 'bad utf-8 error',
			PREG_BAD_UTF8_OFFSET_ERROR => 'bad utf-8 offset error'
		);
		return $codes[$code];
	}

	private static function _throwError($code = null) {
		if($code === null) $code = preg_last_error();
		throw new \RuntimeException(
			'regular expression error: ' . self::errorString($code),
			$code
		);
	}

	private static function _checkError() {
		$code = preg_last_error();
		if($code != PREG_NO_ERROR) {
			self::_throwError($code);
		}
	}

	private static function _match($regex, $str, $flags, $offset) {
		$r = preg_match($regex, $str, $matches, $flags, $offset);
		if($r === false) {
			self::_throwError();
		} elseif($r) {
			return $matches;
		} else {
			return null;
		}
	}

	/**
	 * Try to match a regular expression against a string.
	 *
	 * Tests a regular expression against a string and returns a
	 * `RegexUtilMatch` object, or `null` if there was no match.
	 *
	 * The match at index 0 is the part of the string which matched the
	 * whole pattern.
	 *
	 * @param string $regex The regular expression.
	 * @param string $str The string.
	 * @param int $offset An optional starting offset.
	 * @return \Jitsu\RegexUtilMatch|null
	 * @throws \RuntimeException Thrown if the regular expression is not
	 *                           valid.
	 */
	public static function match($regex, $str, $offset = 0) {
		$r = self::_match($regex, $str, 0, $offset);
		if($r !== null) $r = new RegexUtilMatch($r);
		return $r;
	}

	/**
	 * Like `match`, but also include the starting indices of the matches.
	 *
	 * If the string matches the regular expression, the return value
	 * includes the starting indices of the matches as well. A starting
	 * index of -1 indicates that the group was not matched.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int $offset
	 * @return \Jitsu\RegexUtilMatch|null
	 * @throws \RuntimeException
	 */
	public static function matchWithOffsets($regex, $str, $offset = 0) {
		$r = self::_match($regex, $str, PREG_OFFSET_CAPTURE, $offset);
		if($r !== null) {
			$r = new RegexUtilMatchWithOffsets(
				array_column($r, 0),
				array_column($r, 1)
			);
		}
		return $r;
	}

	/**
	 * Get all non-overlapping matches of a regular expression in a string.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int $offset
	 * @return \Jitsu\RegexUtilMatch[]
	 * @throws \RuntimeException
	 */
	public static function matchAll($regex, $str, $offset = 0) {
		$r = preg_match_all($regex, $str, $matches, PREG_SET_ORDER, $offset);
		if($r === false) {
			self::_throwError();
		}
		$match_objs = array();
		foreach($matches as $match_set) {
			$match_objs[] = new RegexUtilMatch($match_set);
		}
		return $match_objs;
	}

	/**
	 * Get all non-overlapping matches of a regular expression in a string
	 * along with offset information.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int $offset
	 * @return \Jitsu\RegexUtilMatch[]
	 * @throws \RuntimeException
	 */
	public static function matchAllWithOffsets($regex, $str, $offset = 0) {
		$r = preg_match_all(
			$regex, $str, $matches,
			PREG_SET_ORDER | PREG_OFFSET_CAPTURE,
			$offset
		);
		if($r === false) {
			self::_throwError();
		}
		$match_objs = array();
		foreach($matches as $match_set) {
			$match_objs[] = new RegexUtilMatchWithOffsets(
				array_column($match_set, 0),
				array_column($match_set, 1)
			);
		}
		return $match_objs;
	}

	/**
	 * Escape a string for interpolation in a regular expression.
	 *
	 * If used with a pattern where the delimiter is being explicitly set,
	 * you must provide that delimiter as the second argument.
	 *
	 * @param string $str
	 * @param string|null $delim
	 * @return string
	 */
	public static function escape($str, $delim = null) {
		return preg_quote($str, $delim);
	}

	/**
	 * Replace the portion of a string which matches a regular expression
	 * with another string.
	 *
	 * The replacement string may use backreferences in the form `\n`,
	 * `$n`, or `${n}`. Optionally specify a limit for the number of
	 * replacements which may be made (pass `null` for unlimited).
	 *
	 * Stores the number of replacements made in the optional `$count`
	 * variable.
	 *
	 * If `$replacement` is not a string or array, it will be interpreted
	 * as a callback with the signature `function($matches)` whose return
	 * value will be used to generate the replacement strings. The
	 * `$matches` parameter is an array containing the matched groups.
	 *
	 * Any one of the arguments `$regex`, `$replacement`, or `$str` may
	 * be an array of multiple values. Whenever each is a scalar, it
	 * applies to all the values in the other arguments, be they scalars or
	 * arrays. Whenever each is an array, it applies pairwise to the other
	 * array arguments.
	 *
	 * When `$regex` is an array tested against a scalar `$str`, all of the
	 * patterns are tested as a logical "or".
	 *
	 * When `$replacement` is an array with too few elements for the other
	 * array arguments, the missing values are assumed to be the empty
	 * string. If it is an array, it may contain only strings, not
	 * callbacks.
	 *
	 * When `$str` is an array, an array of the replaced strings is
	 * returned.
	 *
	 * @param string|string[] $regex
	 * @param string|string[] $str
	 * @param string|string[]|callable $replacement
	 * @param int|null $limit
	 * @return string|string[]
	 * @throws \RuntimeException
	 */
	public static function replace($regex, $str, $replacement, $limit = null) {
		return self::_replace($regex, $replacement, $str, $limit, false);
	}

	/**
	 * Like `replace`, but include the number of replacements as a second
	 * return value.
	 *
	 * @param string|string[] $regex
	 * @param string|string[] $str
	 * @param string|string[]|callable $replacement
	 * @param int|null $limit
	 * @return array The pair `array($replaced, $count)`.
	 * @throws \RuntimeException
	 */
	public static function replaceAndCount($regex, $str, $replacement, $limit = null) {
		return self::_replace($regex, $replacement, $str, $limit, true);
	}

	private static function _replace($regex, $replacement, $str, $limit, $use_count) {
		if(!is_string($replacement) && !is_array($replacement)) {
			return self::_replaceWith($regex, $replacement, $str, $limit, $use_count);
		} else {
			if($limit === null) $limit = -1;
			$r = (
				$use_count ?
				preg_replace($regex, $replacement, $str, $limit, $count) :
				preg_replace($regex, $replacement, $str, $limit)
			);
			if($r === null) {
				self::_throwError();
			}
			return $use_count ? array($r, $count) : $r;
		}
	}

	/**
	 * Like `replace`, except that the second parameter is always
	 * interpreted as a callback.
	 *
	 * This allows function names, etc. to be passed.
	 *
	 * @param string|string[] $regex
	 * @param string|string[] $str
	 * @param callable $callback
	 * @param int|null $limit
	 * @return string|string[]
	 * @throws \RuntimeException
	 */
	public static function replaceWith($regex, $str, $callback, $limit = null) {
		return self::_replaceWith($regex, $callback, $str, $limit, false);
	}

	/**
	 * Like `replaceWith` but with the number of replacements included.
	 *
	 * @param string|string[] $regex
	 * @param string|string[] $str
	 * @param callable $callback
	 * @param int|null $limit
	 * @return array The pair `array($replaced, $count)`.
	 * @throws \RuntimeException
	 */
	public static function replaceAndCountWith($regex, $str, $callback, $limit = null) {
		return self::_replaceWith($regex, $callback, $str, $limit, true);
	}

	private static function _replaceWith($regex, $callback, $str, $limit, $use_count) {
		if($limit === null) $limit = -1;
		$r = (
			$use_count ?
			preg_replace_callback($regex, $callback, $str, $limit, $count) :
			preg_replace_callback($regex, $callback, $str, $limit)
		);
		if($r === null) {
			self::_throwError();
		}
		return $use_count ? array($r, $count) : $r;
	}

	/**
	 * Same behavior as `replace`, except that when `$strs` is an array,
	 * only strings which had a replacement performed are returned in the
	 * resulting array.
	 *
	 * @param string|string[] $regex
	 * @param string|string[] $strs
	 * @param string|string[]|callable $replacement
	 * @param int|null $limit
	 * @return string|string[]
	 * @throws \RuntimeException
	 */
	public static function replaceAndFilter($regex, $strs, $replacement, $limit = null) {
		return self::_replaceFilter($regex, $replacement, $strs, $limit, false);
	}

	/**
	 * Like `replaceAndFilter` but with the number of replacements included.
	 *
	 * @param string|string[] $regex
	 * @param string|string[] $strs
	 * @param string|string[]|callable $replacement
	 * @param int|null $limit
	 * @return array The pair `array($replaced, $count)`.
	 * @throws \RuntimeException
	 */
	public static function replaceAndFilterAndCount($regex, $strs, $replacement, $limit = null) {
		return self::_replaceFilter($regex, $replacement, $strs, $limit, true);
	}

	private static function _replaceFilter($regex, $replacement, $str, $limit, $use_count) {
		if($limit === null) $limit = -1;
		$r = (
			$use_count ?
			preg_filter($regexes, $replacements, $strs, $limit, $count) :
			preg_filter($regexes, $replacements, $strs, $limit)
		);
		self::_checkError();
		return $use_count ? array($r, $count) : $r;
	}

	/**
	 * Test a regular expression against an array of strings and return
	 * those strings which match.
	 *
	 * @param string $regex
	 * @param string[] $strs
	 * @return string[] The result is not re-indexed.
	 * @throws \RuntimeException
	 */
	public static function grep($regex, $strs) {
		$r = preg_grep($regex, $strs);
		self::_checkError();
		return $r;
	}

	/**
	 * Same as `grep`, except that all of the strings which do *not* match
	 * the regular expression are returned.
	 *
	 * @param string $regex
	 * @param string[] $strs
	 * @return string[] The result is not re-indexed.
	 * @throws \RuntimeException
	 */
	public static function invertedGrep($regex, $strs) {
		$r = preg_grep($regex, $strs, PREG_GREP_INVERT);
		self::_checkError();
		return $r;
	}

	private static function _split($regex, $str, $limit, $flags, $use_offsets) {
		if($use_offsets) $flags |= PREG_SPLIT_OFFSET_CAPTURE;
		$r = preg_split($regex, $str, $limit, $flags);
		self::_checkError();
		if($use_offsets) {
			return array(array_column($r, 0), array_column($r, 1));
		} else {
			return $r;
		}
	}

	/**
	 * Split a string by a regular expression. Optionally provide a limit
	 * to the number of splits.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int|null $limit
	 * @return string[]
	 * @throws \RuntimeException
	 */
	public static function split($regex, $str, $limit = null) {
		return self::_split($regex, $str, $limit, 0, false);
	}

	/**
	 * Like `split` but with offsets included as a second return value.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int|null $limit
	 * @return array The pair `array($parts, $offsets)`.
	 * @throws \RuntimeException
	 */
	public static function splitWithOffsets($regex, $str, $limit = null) {
		return self::_split($regex, $str, $limit, 0, true);
	}

	/**
	 * Like `split`, but filters out empty strings from the result.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int|null $limit
	 * @return string[]
	 * @throws \RuntimeException
	 */
	public static function splitAndFilter($regex, $str, $limit = null) {
		return self::_split($regex, $str, $limit, PREG_SPLIT_NO_EMPTY, false);
	}

	/**
	 * Like `splitAndFilter` but with offsets included as a second return
	 * value.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int|null $limit
	 * @return array The pair `array($parts, $offsets)`.
	 * @throws \RuntimeException
	 */
	public static function splitAndFilterWithOffsets($regex, $str, $limit = null) {
		return self::_split($regex, $str, $limit, PREG_SPLIT_NO_EMPTY, true);
	}

	/**
	 * Like `split`, except include group 1 of the splitting pattern in the
	 * results as well.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int|null $limit
	 * @return string[]
	 * @throws \RuntimeException
	 */
	public static function inclusiveSplit($regex, $str, $limit = null) {
		return self::_split($regex, $str, $limit, PREG_SPLIT_DELIM_CAPTURE, false);
	}

	/**
	 * Like `inclusiveSplit` but with offsets included as a second return
	 * value.
	 *
	 * @param string $regex
	 * @param string $str
	 * @param int|null $limit
	 * @return array The pair `array($parts, $offsets)`.
	 * @throws \RuntimeException
	 */
	public static function inclusiveSplitWithOffsets($regex, $str, $limit = null) {
		return self::_split($regex, $str, $limit, PREG_SPLIT_DELIM_CAPTURE, true);
	}
}
