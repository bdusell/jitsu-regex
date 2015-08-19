<?php

namespace Jitsu;

class RegexUtilMatchWithOffsets {

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
