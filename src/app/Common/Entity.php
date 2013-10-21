<?php

namespace Common;

class Entity {
	public function getKey() {
		return $this->key;
	}

	public function getValue() {
		return $this->value;
	}

	public function setKey($key) {
		$this->key = $key;
	}

	public function setValue($value) {
		$this->value = $value;
	}
}