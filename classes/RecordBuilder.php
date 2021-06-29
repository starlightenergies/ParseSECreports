<?php

namespace JDApp;
use JDApp\StateMachine as State;
use JDApp\TaxonomyTerms as Terms;

class RecordBuilder {

	public object $State;
	public object $Terms;
	public int $unit_flag;
	public int $data_type_flag;

	public function __construct(State $S ) {
		$this->State = $S;
		$this->unit_flag = 0;
		$this->data_type_flag = 0;
	}

	public function processEntry($Record, $char) {

		$StateE = $this->State;

		switch ($StateE->task) {
			case CREATEENTRY:
				$currentObject = $Record->currentObjectId;
				$data = $Record->dataStore[$currentObject]->data_units;
				$Record->dataStore[$currentObject]->createEntry($data);
				$dataObj =$Record->dataStore[$currentObject];
				$StateE->task = MAKEKEY;
				break;
			case MAKEKEY:
				$this->updateEntryKey($Record, $char);
				break;
			case MAKEVALUE:
				$this->updateEntryValue($Record, $char);
				break;
			case STOREKEYVAL:
				$this->storeEntryKeyAndValue($Record, $StateE);
				$StateE->task = MAKEKEY;
				break;
			default:
				echo NEEDSWORK . $StateE->char_id . "\n";
				sleep(3600);
		}
	}

	public function processData($Record, $char) {

		$StateM = $this->State;
		$TermsT = $this->Terms;

		switch ($StateM->task) {
			case MAKEKEY:
				$this->updateKey($Record, $char,$TermsT);
				break;
			case MAKEVALUE:
				$this->updateValue($Record, $char);
				break;
			case STOREKEY:
				if($this->data_type_flag == 1) {									//TODO data flag

				} else {
					$StateM->task = MAKEKEY;
				}
				break;
			case STOREKEYVAL:
				switch ($Record->key_name) {
					case 'label':
						$curr_data = $Record->dataStore[$Record->currentObjectId];
						$curr_data->label = $Record->key_value;
						$curr_data->updateCompletionStatus();
						$Record->key_value = '';
						$Record->key_name = '';
						$StateM->task = MAKEKEY;
						break;
					case 'description':
						$curr_data = $Record->dataStore[$Record->currentObjectId];
						$curr_data->description = $Record->key_value;
						$curr_data->updateCompletionStatus();										//entries started 5/5
						$Record->key_value = '';
						$Record->key_name = '';
						if ($curr_data->completion_status == 5){									//once data object finished make a new one.
							$StateM->datatype = HEADER;
							$StateM->task = MAKENEWDATAOBJECT;
							echo "here i am in [data] description header make new obj " . $curr_data->completion_status . "char = " . $char . "\n";
							sleep(60);
						} else {
							$StateM->task = MAKEKEY;
							echo "here i am in [data] description header make new key " . $curr_data->completion_status . "char = " . $char .
								"\n";
							sleep(60);
						}
						break;
					default:
				}
				break;
			default:
		}

	}

	public function processHeader($Record, $char) {

		$State = $this->State;

		switch ($State->task) {
			case MAKEKEY:
				$this->updateKey($Record, $char);
				break;
			case MAKEVALUE:
				$this->updateValue($Record, $char);										//clean. ok.
				break;
			case MAKENEWDATAOBJECT:
				$State->datatype = DATA;
				$State->task = MAKEKEY;
				$currentObject = $Record->currentObjectId;
				$data = $Record->dataStore[$currentObject];
				$obj_id = $Record->createDataObject($data->taxonomy);
				echo "here i am in [header] make new obj " . $data->completion_status . " with char = " . $char . "\n";
				sleep(60);
				$this->data_type_flag = 1;											//it TODO almost always the first to be created after obj created
				break;																//TODO change in taxonomy can come before data type..
			case STOREKEYVAL:														//ok set by comma
			case STOREKEY:															//ok set by left brace
				switch ($Record->key_name) {
					case SEC_ID:
						$Record->cik = $Record->key_value;
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$this->Terms = new TaxonomyTerms($Record->cik);							//NEW LOGIC METHOD TODO and Test
						$State->task = MAKEKEY;
						break;
					case COMPANY_NAME:
						$Record->company_name = $Record->key_value;
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$State->task = MAKEKEY;
						break;
					case DOC_ENTITY_TYPE:
						$State->datatype = DATA;
						$State->task = CREATEDATAOBJ;
						$Record->createDataObject(DOC_ENTITY_TYPE);
						$header[$Record->key_name] = $Record->key_value;				//???TODO
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$State->task = MAKEKEY;
						break;
				//	case US_GAAP_TYPE:
				//		$State->datatype = DATA;
					case FACTS:
						$Record->key_name = '';
						$State->task = MAKEKEY;
						break;
					//case "ifrs":
					//case "srt":
					default:
						echo NEEDSWORK . $State->char_id . "\n";
						sleep(3600);
				}
			break;
		default:
			echo NEEDSWORK;
		}
	}


	public function updateKey($R, $c, $terms=null) {
		//get existing key, update it with curr character
		//R is Record object, c - character
		$data_id = $R->currentObjectId;										//ok

		if($data_id >= 0) {
			$curr_object = @$R->dataStore[$data_id];                                //could use switch for data_units etc
		}																			//Tags like units always come after Datatype TODO
		 																			//Need to store last key in a buffer and identify after fact TODO
		if($terms != null) {
			$dataTermsArray = $terms->getDataTerms();
			$headerTermsArray = $terms->getHeaderTerms();
		}
		if ($data_id == 0) {												//ok
			$key = $R->key_name;											//ok
			$key .= $c;														//ok
			$R->key_name = $key;											//ok
		} elseif($data_id > 0) {
			$key = $R->key_name;                                            //ok used as temp holding area
			$key .= $c;                                                        //ok
			$R->key_name = ltrim($key);										//removes any leading whitespace
			//new test needs work
			if (in_array($R->key_name, $dataTermsArray)||in_array($R->key_name, $headerTermsArray)) {                        //may need to pull this out into new method TODO
				switch ($R->key_name) {
					case 'label':
						break;                        //TODO
					case 'description':
						break;                        //TODO
					case 'facts':
						break;
					case 'units':
						$this->unit_flag = 1;                                //flag can be useful
						$R->key_name = '';
						break;
					case 'shares':
					case 'USD':
					case 'sqft':
					case 'pure':
					case 'D':
						$curr_object->data_units = $R->key_name;
						$this->unit_flag = 0;
						$R->key_name = '';
						break;
					case 'us-gaap':                                            //NEW TODO
						$result = $curr_object->setTaxonomy($R->key_name);            //update with correct taxonomy
						$R->key_name = '';
						$curr_object->data_change_flag = $result;
						break;
					default:
						$curr_object->data_type = $R->key_name;
						$curr_object->updateCompletionStatus();
						$R->key_name = '';
						$this->data_type_flag = 0;								//TODO datatpe key
				}                                                                //else if in other terms groups TODO
				//$R->key_name = '';                                        //switch units, etc to ignore and use

			}
		}
	}




	public function updateValue($R, $c) {
		//get existing value, update it with curr character
		//R is Record object, c - character
		$value = $R->key_value;
		$value .= $c;
		$R->key_value = ltrim($value);
	}


	public function updateEntryKey($R, $c) {
		$dataId = $R->currentObjectId;
		$data = $R->dataStore[$dataId];
		$entry = $data->entryStore[$data->currentEntryId];
		$value = $entry->current_key;
		$value .= $c;
		$entry->current_key = ltrim($value);
	}

	public function updateEntryValue($R, $c) {
		$dataId = $R->currentObjectId;
		$data = $R->dataStore[$dataId];
		$entry = $data->entryStore[$data->currentEntryId];
 		$entry->current_value .= $c;
	}

	public function storeEntryKeyAndValue($R,$S) {

		$data = $R->dataStore[$R->currentObjectId];
		$entry = $data->entryStore[$data->currentEntryId];
		$result = $entry->insertKey();
		$result = $entry->insertValue();                                    //need to error check here TODO

		if ($data->completion_status == 5){									//once data object finished make a new one.
			$S->datatype = HEADER;
			$S->task = MAKENEWDATAOBJECT;
		}
	}



} //end of class


/*





	if ($State->datatype == HEADER) {
		switch ($State->task) {
			case MAKEKEY:
				updateKey($Record, $char, $State);
				break;
			case MAKEVALUE:
				updateValue($Record, $char);
				break;
			case MAKENEWDATAOBJECT:
				$State->datatype = DATA;
				$State->task = CREATEDATAOBJ;
				$currentObject = $Record->currentObjectId;
				$data = $Record->dataStore[$currentObject];
				$obj_id = $Record->createDataObject($data->taxonomy);
				break;
			case STOREKEYVAL:
			case STOREKEY:
				switch ($Record->key_name) {
					case SEC_ID:
						$Record->cik = $Record->key_value;
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$Record->task = MAKEKEY;
						break;
					case COMPANY_NAME:
						$Record->company_name = $Record->key_value;
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$State->task = MAKEKEY;
						break;
					case DOC_ENTITY_TYPE:
						$State->datatype = DATA;
						$State->task = CREATEDATAOBJ;
						$obj_id = $Record->createDataObject(DOC_ENTITY_TYPE);
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						break;
					case US_GAAP_TYPE:
						$State->datatype = DATA;
					case FACTS:
						$Record->key_name = '';
						$State->braces -= 1;            //put in to address diff style in intuit report TODO
						$State->task = MAKEKEY;
						break;
					case DATALABEL:                                //new
						$Record->key_name = '';
						$State->task = MAKELABELVALUE;
						break;
					case DESCRIPLABEL:                            //new
						$Record->key_name = '';
						$State->task = MAKEDESCRIPTIONVALUE;
						break;
					case UNITS:                                    //new
						$Record->key_name = '';
						$State->task = MAKEKEY;
						break;

					//case "ifrs":
					//case "srt":
					default:
						echo NEEDSWORK . $State->char_id . "\n";
						sleep(3600);

				}
				break;

			default:
				echo NEEDSWORK;
		}

	} elseif ($State->datatype == DATA) {
		switch ($State->task) {
			case MAKELABEL:
				updateLabelKey($State);
				break;
			case MAKEKEY:
				updateKey($Record, $char, $State);
				break;
			case STORETAXONOMYKEY:
				$id = $Record->currentObjectId;
				$data = $Record->dataStore[$id];
				$data->setTaxonomy($char);
				break;
			case MAKEVALUE:
			case STOREKEY:
				checkBraces($State);
				break;
			case MAKELABELVALUE:
				updateLabelValue($Record, $char, $State);
				break;
			case MAKEDESCRIPTIONVALUE:
				updateDescriptionValue($Record, $char, $State);
				break;
			case DATALABEL:                                //new
				$Record->key_name = '';
				$State->task = MAKELABELVALUE;
				break;
			case DESCRIPLABEL:                            //new
				$Record->key_name = '';
				$State->task = MAKEDESCRIPTIONVALUE;
				break;
			case UNITS:                                    //new
				$Record->key_name = '';
				$State->task = MAKEKEY;
				break;

			default:
				echo NEEDSWORK . $State->char_id . "\n";
				sleep(3600);
		}

	} elseif ($State->datatype == ENTRY) {

		switch ($State->task) {
			case CREATEENTRY:
				$currentObject = $Record->currentObjectId;
				$data = $Record->dataStore[$currentObject]->data_units;
				$id = $Record->dataStore[$currentObject]->createEntry($data);
				$State->task = MAKEKEY;
				break;
			case MAKEKEY:
				updateEntryKey($Record, $char);
				break;
			case MAKEVALUE:
				updateEntryValue($Record, $char);
				break;
			case STOREKEY:
				checkBraces($State);
				break;
			case STOREKEYVAL:
				storeEntryKeyAndValue($Record);
				break;
			default:
				echo NEEDSWORK . $State->char_id . "\n";
				sleep(3600);
		}
	}
	return $Record;
}

function updateLabelKey($S)
{

	//label already has a property in data object so
	//just ignore it

	if ($S->braces == 3) {
		$S->task = IGNORE;
	}

}

function updateDescriptionValue($R, $c, $S)
{

	if ($S->quotes == 1) {
		$curr_object = $R->dataStore[$R->currentObjectId];
		$descrip = $curr_object->description;
		$descrip .= $c;
		$curr_object->description = $descrip;
	}
}


function updateLabelValue($R, $c, $S)
{

	if ($S->quotes == 1) {
		$curr_object = $R->dataStore[$R->currentObjectId];
		$label = $curr_object->label;
		$label .= $c;
		$curr_object->label = $label;
	}
}

function storeEntryKeyAndValue($R)
{

	$data = $R->dataStore[$R->currentObjectId];
	$entry = $data->entryStore[$data->currentEntryId];
	$results = $entry->insertKey();
	$results = $entry->insertValue();                                    //need to error check here TODO
}


function updateEntryValue($R, $c)
{

	$dataId = $R->currentObjectId;
	$data = $R->dataStore[$dataId];
	$entry = $data->entryStore[$data->currentEntryId];
	$entry->current_value .= $c;

}


function updateEntryKey($R, $c)
{

	$dataId = $R->currentObjectId;
	$data = $R->dataStore[$dataId];
	$entry = $data->entryStore[$data->currentEntryId];
	$entry->current_key .= $c;

}


function checkBraces($S)
{
	//S is State object

	if ($S->braces == 3) {
		$S->task = MAKEKEY;                                //was IGNORE
	} elseif ($S->braces >= 4 || $S->braces == 2) {
		$S->task = MAKEKEY;
	}
}

function log()
{
	echo NEEDSWORK;
}

function updateValue($R, $c)
{
	//get existing value, update it with curr character
	//R is Record object, c - character
	$value = $R->key_value;
	$value .= $c;
	$R->key_value = $value;
}

function updateKey($R, $c, $S)
{
	//get existing key, update it with curr character
	//R is Record object, c - character
	$data_id = $R->currentObjectId;
	if ($data_id == 0 && $S->braces != 4 && $c != '{') {
		$key = $R->key_name;
		$key .= $c;
		$R->key_name = $key;
	} else {
		if ($S->braces == 2) {
			$curr_object = $R->dataStore[$data_id];
			$type = $curr_object->data_type;
			$type .= $c;
			$curr_object->data_type = $type;
		} elseif ($S->braces == 4) {
			$curr_object = $R->dataStore[$data_id];
			$type = $curr_object->data_units;
			$type .= $c;
			$curr_object->data_units = $type;
		}
	}
}


*/