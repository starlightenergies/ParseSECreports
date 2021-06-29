<?php
namespace JDApp;


class Data {

	public int $id;
	public string $taxonomy;
	public static int $data_id = 0;
	public string $data_type;
	public string $data_units;
	public string $label;
	public string $description;
	public array $entryStore = [];
	public int $currentEntryId;
	public int $completion_status;
	public int $data_change_flag;

	public function __construct($taxo) {
		$this->taxonomy = $taxo;
		$this->id = 1 + DATA::$data_id++;
		$this->data_type = '';
		$this->data_units = '';
		$this->label = '';
		$this->description = '';
		$this->currentEntryId = 0;
		$this->completion_status = 0;
		$this->data_change_flag = 0;


	}

	public function setTaxonomy($taxo): int {

		//set new and check it
		$this->taxonomy = $taxo;
		if($this->taxonomy === $taxo) {
			return 1;
		} else {
			return ZERO;
		}

	}

	public function updateCompletionStatus(): int {

		// when this reaches 7 then a new data object can be started
		//taxonomy, type, units, label, description, entries, curr_id
		return $this->completion_status += 1;
	}

	public function getId(): int 	{
		return $this->id;
	}

	public function createEntry($name) {

		$entry = new Entry($name);
		$entry_id = $entry->getId();
		$this->entryStore[$entry_id] = $entry;
		$this->currentEntryId = $entry_id;
	}

}