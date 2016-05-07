jitsu/regex
------------

The `Jitsu\RegexUtil` class is a collection of static methods for dealing with
regular expressions in PHP. These helper functions simplify the creation of new
patterns, escaping literal strings, handling errors, and accessing match
offsets.

This package is part of [Jitsu](https://github.com/bdusell/jitsu).

## Installation

Install this package with [Composer](https://getcomposer.org/):

```sh
composer require jitsu/regex
```

## Namespace

The class is defined under the namespace `Jitsu`.

## API

### class Jitsu\\RegexUtil

A collection of static methods for dealing with regular expressions.

#### RegexUtil::create($pat, $flags = '', $start = null, $end = null)

Create a regular expression from a PCRE pattern.

This converts a string containing a PCRE pattern to a value
compatible for use with this module. Do not include the delimiters;
they will be added and escaped automatically. You can specify any
flags in a separate string. If you know that your PCRE is already
properly escaped with respect to a certain set of deliters, you can
optionally provide the start and ending delimiters to use and avoid
the overhead of escaping the pattern.

|   | Type | Description |
|---|------|-------------|
| **`$pat`** | `string` | A PCRE pattern with *no* delimiters. |
| **`$flags`** | `string` | Optional PCRE flags. |
| **`$start`** | `string|null` | An optional start delimiter to use. |
| **`$end`** | `string|null` | An optional end delimiter to use. Defaults to the start delimiter. |
| returns | `string` |  |

#### RegexUtil::errorString($code)

Get an error string for a `PREG_` error code.

|   | Type | Description |
|---|------|-------------|
| **`$code`** | `int` | A `PREG_` error code. |
| returns | `string` |  |

#### RegexUtil::match($regex, $str, $offset = 0)

Try to match a regular expression against a string.

Tests a regular expression against a string and returns a
`RegexUtilMatch` object, or `null` if there was no match.

The match at index 0 is the part of the string which matched the
whole pattern.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string` | The regular expression. |
| **`$str`** | `string` | The string. |
| **`$offset`** | `int` | An optional starting offset. |
| returns | `\Jitsu\RegexUtilMatch|null` |  |
| throws | `\RuntimeException` | Thrown if the regular expression is not valid. |

#### RegexUtil::matchWithOffsets($regex, $str, $offset = 0)

Like `match`, but also include the starting indices of the matches.

If the string matches the regular expression, the return value
includes the starting indices of the matches as well. A starting
index of -1 indicates that the group was not matched.

|   | Type |
|---|------|
| **`$regex`** | `string` |
| **`$str`** | `string` |
| **`$offset`** | `int` |
| returns | `\Jitsu\RegexUtilMatch|null` |
| throws | `\RuntimeException` |

#### RegexUtil::matchAll($regex, $str, $offset = 0)

Get all non-overlapping matches of a regular expression in a string.

|   | Type |
|---|------|
| **`$regex`** | `string` |
| **`$str`** | `string` |
| **`$offset`** | `int` |
| returns | `\Jitsu\RegexUtilMatch[]` |
| throws | `\RuntimeException` |

#### RegexUtil::matchAllWithOffsets($regex, $str, $offset = 0)

Get all non-overlapping matches of a regular expression in a string
along with offset information.

|   | Type |
|---|------|
| **`$regex`** | `string` |
| **`$str`** | `string` |
| **`$offset`** | `int` |
| returns | `\Jitsu\RegexUtilMatch[]` |
| throws | `\RuntimeException` |

#### RegexUtil::escape($str, $delim = null)

Escape a string for interpolation in a regular expression.

If used with a pattern where the delimiter is being explicitly set,
you must provide that delimiter as the second argument.

|   | Type |
|---|------|
| **`$str`** | `string` |
| **`$delim`** | `string|null` |
| returns | `string` |

#### RegexUtil::replace($regex, $str, $replacement, $limit = null)

Replace the portion of a string which matches a regular expression
with another string.

The replacement string may use backreferences in the form `\n`,
`$n`, or `${n}`. Optionally specify a limit for the number of
replacements which may be made (pass `null` for unlimited).

Stores the number of replacements made in the optional `$count`
variable.

If `$replacement` is not a string or array, it will be interpreted
as a callback with the signature `function($matches)` whose return
value will be used to generate the replacement strings. The
`$matches` parameter is an array containing the matched groups.

Any one of the arguments `$regex`, `$replacement`, or `$str` may
be an array of multiple values. Whenever each is a scalar, it
applies to all the values in the other arguments, be they scalars or
arrays. Whenever each is an array, it applies pairwise to the other
array arguments.

When `$regex` is an array tested against a scalar `$str`, all of the
patterns are tested as a logical "or".

When `$replacement` is an array with too few elements for the other
array arguments, the missing values are assumed to be the empty
string. If it is an array, it may contain only strings, not
callbacks.

When `$str` is an array, an array of the replaced strings is
returned.

|   | Type |
|---|------|
| **`$regex`** | `string|string[]` |
| **`$str`** | `string|string[]` |
| **`$replacement`** | `string|string[]|callable` |
| **`$limit`** | `int|null` |
| returns | `string|string[]` |
| throws | `\RuntimeException` |

#### RegexUtil::replaceAndCount($regex, $str, $replacement, $limit = null)

Like `replace`, but include the number of replacements as a second
return value.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string|string[]` |  |
| **`$str`** | `string|string[]` |  |
| **`$replacement`** | `string|string[]|callable` |  |
| **`$limit`** | `int|null` |  |
| returns | `array` | The pair `array($replaced, $count)`. |
| throws | `\RuntimeException` |  |

#### RegexUtil::replaceWith($regex, $str, $callback, $limit = null)

Like `replace`, except that the second parameter is always
interpreted as a callback.

This allows function names, etc. to be passed.

|   | Type |
|---|------|
| **`$regex`** | `string|string[]` |
| **`$str`** | `string|string[]` |
| **`$callback`** | `callable` |
| **`$limit`** | `int|null` |
| returns | `string|string[]` |
| throws | `\RuntimeException` |

#### RegexUtil::replaceAndCountWith($regex, $str, $callback, $limit = null)

Like `replaceWith` but with the number of replacements included.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string|string[]` |  |
| **`$str`** | `string|string[]` |  |
| **`$callback`** | `callable` |  |
| **`$limit`** | `int|null` |  |
| returns | `array` | The pair `array($replaced, $count)`. |
| throws | `\RuntimeException` |  |

#### RegexUtil::replaceAndFilter($regex, $strs, $replacement, $limit = null)

Same behavior as `replace`, except that when `$strs` is an array,
only strings which had a replacement performed are returned in the
resulting array.

|   | Type |
|---|------|
| **`$regex`** | `string|string[]` |
| **`$strs`** | `string|string[]` |
| **`$replacement`** | `string|string[]|callable` |
| **`$limit`** | `int|null` |
| returns | `string|string[]` |
| throws | `\RuntimeException` |

#### RegexUtil::replaceAndFilterAndCount($regex, $strs, $replacement, $limit = null)

Like `replaceAndFilter` but with the number of replacements included.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string|string[]` |  |
| **`$strs`** | `string|string[]` |  |
| **`$replacement`** | `string|string[]|callable` |  |
| **`$limit`** | `int|null` |  |
| returns | `array` | The pair `array($replaced, $count)`. |
| throws | `\RuntimeException` |  |

#### RegexUtil::grep($regex, $strs)

Test a regular expression against an array of strings and return
those strings which match.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string` |  |
| **`$strs`** | `string[]` |  |
| returns | `string[]` | The result is not re-indexed. |
| throws | `\RuntimeException` |  |

#### RegexUtil::invertedGrep($regex, $strs)

Same as `grep`, except that all of the strings which do *not* match
the regular expression are returned.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string` |  |
| **`$strs`** | `string[]` |  |
| returns | `string[]` | The result is not re-indexed. |
| throws | `\RuntimeException` |  |

#### RegexUtil::split($regex, $str, $limit = null)

Split a string by a regular expression. Optionally provide a limit
to the number of splits.

|   | Type |
|---|------|
| **`$regex`** | `string` |
| **`$str`** | `string` |
| **`$limit`** | `int|null` |
| returns | `string[]` |
| throws | `\RuntimeException` |

#### RegexUtil::splitWithOffsets($regex, $str, $limit = null)

Like `split` but with offsets included as a second return value.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string` |  |
| **`$str`** | `string` |  |
| **`$limit`** | `int|null` |  |
| returns | `array` | The pair `array($parts, $offsets)`. |
| throws | `\RuntimeException` |  |

#### RegexUtil::splitAndFilter($regex, $str, $limit = null)

Like `split`, but filters out empty strings from the result.

|   | Type |
|---|------|
| **`$regex`** | `string` |
| **`$str`** | `string` |
| **`$limit`** | `int|null` |
| returns | `string[]` |
| throws | `\RuntimeException` |

#### RegexUtil::splitAndFilterWithOffsets($regex, $str, $limit = null)

Like `splitAndFilter` but with offsets included as a second return
value.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string` |  |
| **`$str`** | `string` |  |
| **`$limit`** | `int|null` |  |
| returns | `array` | The pair `array($parts, $offsets)`. |
| throws | `\RuntimeException` |  |

#### RegexUtil::inclusiveSplit($regex, $str, $limit = null)

Like `split`, except include group 1 of the splitting pattern in the
results as well.

|   | Type |
|---|------|
| **`$regex`** | `string` |
| **`$str`** | `string` |
| **`$limit`** | `int|null` |
| returns | `string[]` |
| throws | `\RuntimeException` |

#### RegexUtil::inclusiveSplitWithOffsets($regex, $str, $limit = null)

Like `inclusiveSplit` but with offsets included as a second return
value.

|   | Type | Description |
|---|------|-------------|
| **`$regex`** | `string` |  |
| **`$str`** | `string` |  |
| **`$limit`** | `int|null` |  |
| returns | `array` | The pair `array($parts, $offsets)`. |
| throws | `\RuntimeException` |  |

### class Jitsu\\RegexUtilMatch

An object representing a regular expression match.

#### new RegexUtilMatch($groups)

|   | Type |
|---|------|
| **`$groups`** | `array` |

#### $regex\_util\_match->\_\_toString()

#### $regex\_util\_match->groups()

Get the array of matched groups.

|   | Type |
|---|------|
| returns | `array` |

#### $regex\_util\_match->group($i)

Get a certain group.

|   | Type |
|---|------|
| **`$i`** | `int` |
| returns | `string` |

#### $regex\_util\_match->offsets()

Get the array of match offsets.

|   | Type | Description |
|---|------|-------------|
| returns | `array|null` | Null if offsets are not available. |

#### $regex\_util\_match->offset($i)

Get the offset for a certain group.

|   | Type | Description |
|---|------|-------------|
| **`$i`** | `int` |  |
| returns | `int|null` | Null if the offset is not available. |

### class Jitsu\\RegexUtilMatchWithOffsets

Extends `RegexUtilMatch`.

A regular expression match with match offset data.

#### new RegexUtilMatchWithOffsets($groups, $offsets)

#### $regex\_util\_match\_with\_offsets->offsets()

#### $regex\_util\_match\_with\_offsets->offset($i)

