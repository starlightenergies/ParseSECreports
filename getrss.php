<?php
namespace JDApp;
require_once "vendor/autoload.php";
require_once "includes/programDefines.inc";


/**
 * @purpose:	Stores financial data into mysql database
 * @filename:	Database.php
 * @version:  	1.0
 * @lastUpdate:  2021-04-04
 * @author:    	James Danforth <james@workinout.com>
 * @pattern:
 * @since:   	2021-04-04
 * @controller:
 * @view:
 * @mytodo:		dynamic alerts
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:
 */


//export these environment variables from your .bashrc file
//then use them here

$d = trim(shell_exec('echo ${DSN}'));
$u = trim(shell_exec('echo ${USER}'));
$p = trim(shell_exec('echo ${PASS}'));

//get database handle
/*
$db = new JDdatabase($d,$u,$p);
$db->createDBHandle();

$theFile = file_get_contents('allTesladata.json');
$fdp = new FinanceDataParser($theFile);

$charArray = $fdp->createArray();
echo "the file is " . count($charArray) . " characters\n";

$newCharArray = $fdp->swapBrackets();
echo "the new file is " . count($newCharArray) . " characters\n";

$fdp->createFile();
*/

$fh = fopen('data/allTesladata.json','r');
$Record = new FinancialRecord();
$State = new StateMachine();
$State->record_id = FinancialRecord::$record_id;


while (!feof($fh)) {
	//all characters start as default
	$State->key = SWDEFAULT;
	$char = fgetc($fh);
	switch ($char) {
		case '{': $State->key = LEFTBRACE; buildRecord($char,$State,$Record); break;
		case ':': $State->key = COLON; buildRecord($char,$State,$Record); break;
		case ',': $State->key = COMMA; buildRecord($char,$State,$Record); break;
		case '}': $State->key = RIGHTBRACE; buildRecord($char,$State,$Record);break;
		case "[": $State->key = LEFTBRACKET; buildRecord($char,$State,$Record); break;
		case "]": $State->key = RIGHTBRACKET; buildRecord($char,$State,$Record);break;
		case '"': $State->key = DOUBLEQUOTE; buildRecord($char,$State,$Record);break;
		case "\n": break;
		default:  buildRecord($char,$State,$Record);
	}
	displayActivity($char,$State,$Record);
}

fclose($fh);

////////////////////////////////////////////////////////////////////////////////////////APP ABOVE

function displayActivity($c,$S,$R) {

	echo "Company: " . $R->company_name . "\t";
	echo "CIK: " . $R->cik . "\t";
	echo "Key Name: " . $R->key_name . "\t";
	echo "Key Value: " . $R->key_value . "\n";

	echo "Datastore count: " . count($R->dataStore) . "\t";
	echo "Current Data ID: " . $R->currentObjectId . "\t";
	if(count($R->dataStore) > 0) {
		$data_id = $R->currentObjectId;
		$data = $R->dataStore[$data_id];
		$type = $data->data_type;
		echo "Data Object Type: " . $type . "\t";
		echo "Data Units Type: " . $data->data_units . "\t";
		echo "Data Label Value: " . $data->label . "\n";
		echo "Data Description: " . $data->description;  //this is really long string so separate line
		if(strlen($data->description) % 30 == 0 ) {
			echo "\n";
		}
		if(count($data->entryStore) > 0) {
				echo "\nRecordstore count: " . count($data->entryStore) . "\t";
				echo "Current Entry ID: " . $data->currentEntryId . "\t";
				$entry = $data->entryStore[$data->currentEntryId];
				echo "Entry Object Type: " . $entry->name . "\t";
				echo "Entry Key: " . $entry->current_key . "\t";
				echo "Entry Value: " . $entry->current_value . "\t";
				echo "Total Entry KeyVals: " . count($entry->values) . "\n";
			} else
			{ echo "\n"; }
	} else { echo "\n"; }

	echo "Task: " . $S->task . "\t";
	echo "Data Type: " . $S->datatype . "\t";
	echo "State->key: " . $S->key . "\t";
	if ($c != "\n") {
		echo "character: " . $c . "\n";
	}

	echo "character count: " . $S->char_id . "\t";
	echo "brace count: " . $S->braces . "\t";
	echo "quote count: " . $S->quotes . "\n";

	echo "\n\n";

	if( $c == '}' ) {$sleep = 85; sleep($sleep);}


}



function buildRecord($char,$State,$Record) {
	$State->statusMessage();
	switch ($State->key) {

		case LEFTBRACE: $results = $State->handleLeftBrace(); break;
		case RIGHTBRACE: $results = $State->handleRightBrace(); break;
		case DOUBLEQUOTE: $results = $State->handleQuotes(); break;
		case COLON: $results = $State->handleColon(); break;
		case COMMA: $results = $State->handleComma(); break;
		case LEFTBRACKET: $results = $State->handleLeftBracket(); break;
		case RIGHTBRACKET: $results = $State->handleRightBracket();break;
		default: $results = $State->handleDefault();
	}

	//test results and keep going if not zero
	if ($results == GETNEXTCHAR) { return null; }

	if ($State->datatype == HEADER) {
		switch ($State->task) {
			case MAKEKEY:
				updateKey($Record, $char,$State);
				break;
			case MAKEVALUE:
				updateValue($Record, $char);
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
						$State->task = CREATEDATAOBJ;
						$obj_id = $Record->createDataObject(US_GAAP_TYPE);
						$header[$Record->key_name] = $Record->key_value;
						$Record->header[] = $header;
						$Record->key_name = '';
						$Record->key_value = '';
						break;
					//case "ifrs":
					//case "srt":
					default:
						echo NEEDSWORK;

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
			case MAKEVALUE:
			case STOREKEY:
				checkBraces($State);
				break;
			case MAKELABELVALUE:
				updateLabelValue($Record, $char, $State);
				break;
			case MAKEDESCRIPTIONVALUE:
				updateDescriptionValue($Record,$char, $State);
				break;
			default:
				echo NEEDSWORK;
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
				updateEntryValue($Record,$char); break;
			case STOREKEY:
				checkBraces($State);break;
			case STOREKEYVAL:
				storeEntryKeyAndValue($Record);break;
			default: echo NEEDSWORK;
		}
	}
	return $Record;
}

function updateLabelKey($S) {

	//label already has a property in data object so
	//just ignore it

	if($S->braces == 3) {
		$S->task = IGNORE;
	}

}

function updateDescriptionValue($R,$c, $S) {

	if ($S->quotes == 1) {
		$curr_object = $R->dataStore[$R->currentObject_id];
		$descrip = $curr_object->description;
		$descrip .= $c;
		$curr_object->description = $descrip;
	}
}


function updateLabelValue($R,$c, $S) {

	if ($S->quotes == 1) {
		$curr_object = $R->dataStore[$R->currentObject_id];
		$label = $curr_object->label;
		$label .= $c;
		$curr_object->label = $label;
	}
}

function storeEntryKeyAndValue($R) {

	$data = $R->dataStore[$R->currentObjectId];
	$entry = $data->entryStore[$data->currentEntryId];
	$results = $entry->insertKey();
	$results = $entry->insertValue();									//need to error check here TODO
}


function updateEntryValue($R,$c) {

	$dataId = $R->currentObjectId;
	$data = $R->dataStore[$dataId];
	$entry = $data->entryStore[$data->currentEntryId];
	$entry->current_value .= $c;

}


function updateEntryKey($R,$c) {

	$dataId = $R->currentObjectId;
	$data = $R->dataStore[$dataId];
	$entry = $data->entryStore[$data->currentEntryId];
	$entry->current_key .= $c;

}


function checkBraces($S) {
	//S is State object
	if($S->braces == 3) {
		$S->task = IGNORE;
	} elseif ($S->braces >= 4) {
		$S->task = MAKEKEY;
	}
}

function log() {
	echo NEEDSWORK;
}

function updateValue($R,$c) {
	//get existing value, update it with curr character
	//R is Record object, c - character
	$value = $R->key_value;
	$value .= $c;
	$R->key_value = $value;
}

function updateKey($R,$c,$S) {
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


