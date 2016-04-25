<?php

namespace Jitsu;

/**
 * An object representing a regular expression match.
 */
class RegexUtilMatch {

	private $groups;

	/**
	 * @param array $groups
	 */
	public function __construct($groups) {
		$this->groups = $groups;
	}

	public function __toString() {
		return implode(', ', $this->groups);
	}

	/**
	 * Get the array of matched groups.
	 *
	 * @return array
	 */
	public function groups() {
		return $this->groups;
	}

	/**
	 * Get a certain group.
	 *
	 * @param int $i
	 * @return string
	 */
	public function group($i) {
		return $this->groups[$i];
	}

	/**
	 * Get the array of match offsets.
	 *
	 * @return array|null Null if offsets are not available.
	 */
	public function offsets() {
		return null;
	}

	/**
	 * Get the offset for a certain group.
	 *
	 * @param int $i
	 * @return int|null Null if the offset is not available.
	 */
	public function offset($i) {
		return null;
	}
}
