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

	public function __construct($taxo) {
		$this->taxonomy = $taxo;
		$this->id = 1 + DATA::$data_id++;
		$this->data_type = '';
		$this->data_units = '';
		$this->label = '';
		$this->description = '';
		$this->currentEntryId = 0;

	}

	public function setTaxonomy($c) {

		if($this->taxonomy == 'dei') {
			$this->taxonomy = '';
		}
		if(preg_match("/[a-zA-Z0-9\-]/",$c)) {
			$this->taxonomy .= $c;
		}
	}

	public function getId(): int 	{
		return $this->id;
	}

	public function createEntry($name): int {

		$entry = new Entry($name);
		$entry_id = $entry->getId();
		$this->entryStore[$entry_id] = $entry;
		$this->currentEntryId = $entry_id;
		return $entry_id;
	}

}