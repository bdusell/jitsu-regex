<?php

namespace Jitsu;

/**
 * A regular expression match with match offset data.
 */
class RegexUtilMatchWithOffsets extends RegexUtilMatch {

	private $offsets;

	public function __construct($groups, $offsets) {
		parent::__construct($groups);
		$this->offsets = $offsets;
	}

	public function offsets() {
		return $this->offsets;
	}

	public function offset($i) {
		return $this->offsets[$i];
	}
}
