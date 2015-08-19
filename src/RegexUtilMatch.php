<?php

namespace Jitsu;

/**
 * An object representing a regular expression match.
 */
class RegexUtilMatch {

	private $groups;

	public function __construct($groups) {
		$this->groups = $groups;
	}

	public function __toString() {
		return implode(', ', $this->groups);
	}

	public function groups() {
		return $this->groups;
	}

	public function group($i) {
		return $this->groups[$i];
	}

	public function offsets() {
		return null;
	}

	public function offset($i) {
		return null;
	}
}
