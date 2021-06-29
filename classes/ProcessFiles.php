<?php
namespace JDApp;
require "vendor/autoload.php";
require_once "includes/programDefines.inc";
use JDApp\FinancialRecord as Record;
use JDApp\StateMachine as State;
use JDApp\RecordBuilder as Builder;


/**
 * @purpose:    	Manages Storing SEC File data into mysql database
 * @filename:    	ProcessFiles.php
 * @version:    	1.0
 * @lastUpdate:  	2021-06-24
 * @author:        	James Danforth <james@workinout.com>
 * @pattern:
 * @since:    		2021-06-24
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:
 */


class ProcessFiles {

	public string $currentFile;
	public object $Record;
	public object $State;
	public object $Label;
	public object $Builder;


	private const CHAR_TIME = 5;
	private const BRACE_TIME = 10;
	private const BRACKET_TIME = 15;

	public function __construct($file) {
		$this->currentFile = $file;
		$this->Record = new Record();
		$this->State = new State();
		$this->State->record_id = Record::$record_id;
		$this->Label = new Labels(HEADER, $this->Record, $this->State);
		$this->Builder = new Builder($this->State);

	}

	public function createFileHandle($m): object {
		$mode = $m;
		return  new \SplFileObject($this->currentFile, $mode);
	}

	public function examineCharacter($c): int {

		$result = 0;
		$StateM = $this->State;
		$Build = $this->Builder;
		$Recd = $this->Record;
		$StateM->statusMessage();

		switch($c) {
			case '{':
				$StateM->key = LEFTBRACE;
				$result = $StateM->handleLeftBrace();                    //returns 1 when lbrace follows colon ( :{ )
				if ($result == 1 && $StateM->datatype == HEADER) {
					$Build->processHeader($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == DATA) {
					$Build->processData($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				}
				break;
			case '}':
				$StateM->key = RIGHTBRACE;
				$result = $StateM->handleRightBrace();
				if ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				}
				break;
			case '"':
				$StateM->key = DOUBLEQUOTE;
				$result = $StateM->handleQuotes();                    //always returns a GETNEXTCHAR
				break;
			case ':':
				$StateM->key = COLON;
				$result = $StateM->handleColon();                    //should always return MAKEVALUE
				break;
			case ',':
				$StateM->key = COMMA;
				$result = $StateM->handleComma();
				if ($result == 1 && $StateM->datatype == HEADER) {
					$Build->processHeader($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == DATA) {
					$Build->processData($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				}
				break;
			case '[':
				$StateM->key = LEFTBRACKET;
				$result = $StateM->handleLeftBracket();
				if ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				}
				break;
			case ']':
				$StateM->key = RIGHTBRACKET;
				$result = $StateM->handleRightBracket();
				if ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				}
				break;
			case '\n':
				break;
			default:
				$StateM->key = SWDEFAULT;
				$result = $StateM->handleDefault();
				if ($result == 1 && $StateM->datatype == HEADER) {
					$Build->processHeader($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == DATA) {
					$Build->processData($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				}
		}

		$this->displayActivity($c, $StateM, $Recd);
		return $result;
	}


	 public function displayActivity($c, $S, $R) {


		echo "Company: " . $R->company_name . "\t";
		echo "CIK: " . $R->cik . "\t";
		echo "Key Name: " . $R->key_name . "\t";
		echo "Key Value: " . $R->key_value . "\n\n";

		echo "Datastore count: " . count($R->dataStore) . "\t";
		echo "Current Data ID: " . $R->currentObjectId . "\t";
		if (count($R->dataStore) > 0) {
			$data_id = $R->currentObjectId;
			$data = $R->dataStore[$data_id];
			$type = $data->data_type;
			echo "Data Taxonomy: " . $data->taxonomy . "\t";
			echo "Data Object Type: " . $type . "\t";
			echo "Data Units Type: " . $data->data_units . "\n";
			echo "Data Label Value: " . $data->label . "\n";
			echo "Data Description: " . $data->description . "\n";  //this is really long string so separate line
			echo "Data Complete Status: " . $data->completion_status . "\n";

			if (count($data->entryStore) > 0) {
				echo "Recordstore count: " . count($data->entryStore) . "\t";
				echo "Current Entry ID: " . $data->currentEntryId . "\t";
				$entry = $data->entryStore[$data->currentEntryId];
				echo "Entry Object Type: " . $entry->name . "\n";
				echo "Entry Key: " . $entry->current_key . "\t";
				echo "Entry Value: " . $entry->current_value . "\t";
				echo "Total Entry KeyVals: " . count($entry->values) . "\n";
			} else {

				echo "\n";
			}
		} else {

			echo "\n";
		}

		echo "Current Task: " . $S->task . "\t";
		echo "Data Type: " . $S->datatype . "\t";
		echo "State->key: " . $S->key . "\t";
		if ($c != "\n") {
			echo "character: " . $c . "\n";
		}

		echo "character count: " . $S->char_id . "\t";
		echo "brace count: " . $S->braces . "\t";
		echo "colon count: " . $S->colon . "\t";
		echo "quote count: " . $S->quotes . "\t";
		echo "Bracket Flag: " . $S->bracket . "\t";
		echo "Entry Flag: " . $S->entry_flag . "\n\n\n";



		 $theD = function ($R, $S) {
			$d = $R->dataStore;
			foreach ($d as $key => $data) {
				echo $key . " val: " . $data->data_type . " and entries: " . count($data->entryStore) .
					" and units: " . $data->data_units . "\n";

				$entries = $data->entryStore;
				foreach ($entries as $key => $ent) {
					$vals = $ent->values;
					echo "\n";
					echo "file point char: " . $S->char_id . "\n";
					echo "data type field size: " . strlen($data->data_type) . "\n";

					foreach ($vals as $key2 => $values) {
						echo "entry key: " . $key2 . " entry val: " . $values . "\n";
						if (!preg_match("/[A-Z0-9\-]/", $values)) {
							echo "weird character\n";
							sleep(5);
						}
					}
				}

			}
		};

		if ($c == '}'|| $c == '{') {
			sleep(self::BRACE_TIME);
		}
		if ($c == ']') {
			//$theD($R, $S);						//dont need a moment
			sleep(self::BRACKET_TIME);
		}
		if ($S->char_id > 6000) {
			sleep(self::CHAR_TIME);
		} else {
			sleep(0);
		}

	}


}

/*

function buildRecord($char, $State, $Record)
{
	$State->statusMessage();
	switch ($State->key) {

		case LEFTBRACE:
			$results = $State->handleLeftBrace();
			break;
		case RIGHTBRACE:
			$results = $State->handleRightBrace();
			break;
		case DOUBLEQUOTE:
			$results = $State->handleQuotes();
			break;
		case COLON:
			$results = $State->handleColon();
			break;
		case COMMA:
			$results = $State->handleComma();
			break;
		case LEFTBRACKET:
			$results = $State->handleLeftBracket();
			break;
		case RIGHTBRACKET:
			$results = $State->handleRightBracket();
			break;
		case BACKSLASH:
			$results = $State->handleBackSlash();
			break;
		default:
			$results = $State->handleDefault();
	}

*/