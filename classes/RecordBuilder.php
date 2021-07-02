<?php
namespace JDApp;
use JDApp\StateMachine as State;
use JDApp\TaxonomyTerms as Terms;

/* MIT LICENSE
Copyright 2021 StarlightEnergies.com
Permission is hereby granted, free of charge, to any person obtaining a copy of this software
and associated documentation files (the "Software"), to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge, publish, distribute,
sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH
THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * @purpose:    	XBRL Report Processing Application
 * @filename:    	RecordBuilder.php
 * @version:    	1.0
 * @lastUpdate:  	2021-07-01
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:    		2021-06-24
 * @controller:
 * @view:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comments:		Financial Records should be simple to acquire, analyze and draw conclusions from. XBRL defeats these goals.
 */


class RecordBuilder {

	public object $State;
	public object $Terms;
	public int $unit_flag;
	public int $data_type_flag;

	public function __construct(State $S) {
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
				$dataObj = $Record->dataStore[$currentObject];
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

	public function processData($Record, $char): int {

		$result = GETNEXTCHAR;  //default
		$StateM = $this->State;
		$TermsT = $this->Terms;

		switch ($StateM->task) {
			case MAKETAXONOMYKEY:
			case MAKEKEY:
				$this->updateKey($Record, $char);
				break;
			case TESTKEY:											//always called by the COLON since key complete then
				$this->testKey($Record, $TermsT);
				$StateM->task = MAKEVALUE;							//then hand back control to COLON
				break;
			case MAKEVALUE:
				$this->updateValue($Record, $char);
				break;
			case STOREKEY:
				$StateM->task = MAKEKEY;            //ref by left brace prob need to fix TODO
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
						$curr_data->updateCompletionStatus();
						$Record->key_value = '';
						$Record->key_name = '';
						if ($curr_data->completion_status == 6) {
							$StateM->datatype = HEADER;
							$StateM->task = MAKENEWDATAOBJECT;
							$this->data_type_flag = 1;            //its almost always the first to be created after obj created
							$result = CONTINUEPROCESSING;        //causes return to build in Header Loop below...
							if($curr_data->completion_status > 6) {
								echo $StateM->char_id . " has problem with data complete flag\n";
								sleep(20);
							}

						} else {
							$StateM->task = MAKEKEY;
							echo "here i am in [data] description header make new key " . $curr_data->completion_status . "char = " . $char .
								"\n";
							//sleep(60);
						}
						break;
					default:
				}
				break;
			default:
		}
		return $result;
	}

	public function processHeader($Record, $char) {

		$State = $this->State;
		//$TermsT = $this->Terms;

		switch ($State->task) {
			case MAKEKEY:
				$this->updateKey($Record, $char);
				break;
			case MAKEVALUE:
				//$this->testKey($Record, $TermsT);
				$this->updateValue($Record, $char);
				break;
			case MAKENEWDATAOBJECT:
				$State->datatype = DATA;
				//test for state to see if taxo key coming next
				if ($State->braces == 1 && $this->data_type_flag == 1) {
					$State->task = MAKETAXONOMYKEY;                     //tests if next key is taxo key
				} else {
					$State->task = MAKEKEY;                                //next key will be data type key
				}
				$currentObject = $Record->currentObjectId;
				$data = $Record->dataStore[$currentObject];
				echo "Record has descrip: " . $data->description . "\n";
				$obj_id = $Record->createDataObject($data->taxonomy);
				break;
			case STOREKEYVAL:                                                        //ok set by comma
			case STOREKEY:                                                            //ok set by left brace
				switch ($Record->key_name) {
					case SEC_ID:
						$Record->cik = $Record->key_value;
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$this->Terms = new Terms($Record->cik);                            //NEW LOGIC METHOD TODO and Test
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
						$header[$Record->key_name] = $Record->key_value;                //???TODO
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						$State->task = MAKEKEY;
						break;
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

	public function testKey($R, $terms) {

		//R is Record object, terms is Taxonomy Keys class (dictionary of terms)
		//terms initialized when CIK found so could be null

		$data_id = $R->currentObjectId;
		if ($data_id >= 0) {
			$curr_object = @$R->dataStore[$data_id];                 //could use switch for data_units etc
		}
		$dataTermsArray = $terms->getDataTerms();
		$headerTermsArray = $terms->getHeaderTerms();

			if (in_array($R->key_name, $dataTermsArray) || in_array($R->key_name, $headerTermsArray)) {
				switch ($R->key_name) {
					case 'label':
						break;
					case 'description':
						break;
					case 'facts':
						break;
					case 'units':
						$this->unit_flag = 1;                                //flag can be useful
						$R->key_name = '';
						break;
					case 'shares':
					case 'USD':
					case 'USD/shares':
					case 'sqft':
					case 'pure':
					case 'D':
						$curr_object->data_units = $R->key_name;
						$this->unit_flag = 0;
						$R->key_name = '';
						break;
					case 'us-gaap':
						$result = $curr_object->setTaxonomy($R->key_name);            //update with correct taxonomy
						$R->key_name = '';
						$this->data_type_flag = $result;     //flag that next key is data type
						break;
					case 'invest':  //if braces == 2 then key will be taxo key -- TODO
						$result = $curr_object->setTaxonomy($R->key_name);            //update with correct taxonomy
						$R->key_name = '';
						$this->data_type_flag = $result;     //flag that next key is data type
						break;
					default:
						$curr_object->data_type = $R->key_name;
						$curr_object->updateCompletionStatus();							//typically #3 of 6
						$R->key_name = '';
						$curr_object->data_change_flag = 0;
						$this->data_type_flag = 0;
				}                                                            //else if in other terms groups TODO
				//$R->key_name = '';                                        //switch units, etc to ignore and use
				//key found
			} elseif ($this->data_type_flag == 1) {

				//we have a new taxonomy datatype. need to use and store
				$curr_object->data_type = $R->key_name;
				//add term to database
				$terms->addDataTerm($R->key_name);
				//update database file
				$terms->updateTermsDatabase();										//new TODO watch frequency drops as db increases
				//update tracker
				$curr_object->updateCompletionStatus();
				$R->key_name = '';
				$curr_object->data_change_flag = 0;
				$this->data_type_flag = 0;

			} elseif($this->unit_flag == 1) {
				//update units first
				$curr_object->data_units = $R->key_name;
				$this->unit_flag = 0;
				//then add term to database									//needs to be units array soon in taxo TODO
			//	$terms->addDataTerm($R->key_name);							cant add to database until breakout units check above
			//	echo "new key: " . $R->key_name . "\n";						into its own method "testunits" or similar method TODO
				$R->key_name = '';
			}
	}


	public function updateKey($R, $c) {
		//get existing key, update it with curr character
		//R is Record object, c - character
		$key = $R->key_name;
		$key .= $c;
		$R->key_name = ltrim($key);

		//alert if big key length
		if(strlen($R->key_name) > 130 ){
			echo $R->key_name . " in " .  $R->company_name . "\nis size: " . strlen($R->key_name) . "\n";
			sleep(1);
		}
	}

	public function updateValue($R, $c) {
		//get existing value, update it with curr character
		//R is Record object, c - character
		$value = $R->key_value;
		$value .= $c;
		$R->key_value = ltrim($value);

		//alert if big key length
		if(strlen($R->key_value) > 1500 ){
			echo $R->key_value . " in " .  $R->company_name . " \nis size: " . strlen($R->key_value) . "\n";
			sleep(1);
		}
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
//old updateKey routine
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
						$this->data_type_flag = $result;     //flag that next key is data type (2)
						echo "setup new taxo key here\n";
						sleep(30);
						break;
					default:
						$curr_object->data_type = $R->key_name;
						$curr_object->updateCompletionStatus();
						$R->key_name = '';
						echo "setup new data type key thats in db already\n";
						sleep(30);
						$curr_object->data_change_flag = 0;
						$this->data_type_flag = 0;
				}                                                            //else if in other terms groups TODO
				//$R->key_name = '';                                        //switch units, etc to ignore and use
				//key found

*/