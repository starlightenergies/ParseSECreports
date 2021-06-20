<?php
namespace JDApp;


class Entry {

	public int $id;
	public static int $entry_id = 0;
	public string $name;
	public array $keys = [];
	public array $values = [];
	public string $current_key;
	public string $current_value;
	public string $status;

	public function __construct($name) {
		$this->id = 1 + Entry::$entry_id++;
		$this->name = $name;
		$this->current_key = '';
		$this->current_value = '';
		$this->status = 'open';
	}

	public function getId(): int {
		return $this->id;
	}

	public function insertKey(): int {
		if ($this->status == 'open') {
			$this->keys[] = $this->current_key;
			//check if insert succeeded
			if (count($this->keys) > 0) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function insertValue(): int {
		$key = $this->current_key;
		$value = $this->current_value;
		$this->values[$key] = $value;

		//check for insert success
		if(count($this->values) > 0 ) {
			$this->current_key = '';
			$this->current_value = '';
			return 1;
		} else {
			return 0;
		}
	}
}