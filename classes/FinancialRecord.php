<?php
namespace JDApp;

class FinancialRecord
{

	//class properties
	public static int $record_id = 0;

	//header properties
	public int $id;
	public string $company_name;
	public string $cik;                                //company index key (S.E.C.)
	public array $header;                                // array [key] = value
	public string $created_at;                            //timestamp
	public string $key_name;
	public string $key_value;


	//data properties
	public array $dataStore;                            //array of data record batches
	public int $currentObjectId;						//data object currently being worked on


	public function __construct() {

		$this->id = self::$record_id++;
		$this->key_name = '';
		$this->key_value = '';
		$this->dataStore = [];
		$this->currentObjectId = 0;
		$this->cik = '';
		$this->company_name = '';
	}


	public function createDataObject($taxo) {

		$dataObject = new Data($taxo);
		$status = $dataObject->updateCompletionStatus();			//taxonomy property updated 1/7
		$object_id = $dataObject->getId();
		$this->dataStore[$object_id] = $dataObject;
		$this->currentObjectId = $object_id;
		$status = $dataObject->updateCompletionStatus();			//curr_id property updated 2/7
	}
}

