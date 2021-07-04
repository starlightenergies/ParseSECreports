<?php
namespace JDApp;
require "vendor/autoload.php";
require_once "includes/programDefines.inc";
use JDApp\TaxonomyTerms as Terms;
use JDApp\FinancialRecord as Record;

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
 * @filename:    	Activity.php
 * @version:    	1.0
 * @lastUpdate:  	2021-07-02
 * @author:        	James Danforth <james@reemotex.com>
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


class Activity {

	private const CHAR_TIME = 0;
	private const BRACE_TIME = 0;
	private const BRACKET_TIME = 0;
	private object $taxoTerms;
	private object $Rec;

	public function __construct (Record $R) {
		$this->Rec = $R;
		$this->taxoTerms = new Terms(123);


	}

	public function collectedDataSummary($R,$S) {

		//method called when reach end of file by ReportProcessor
		$dataObjects = $R->dataStore;
			foreach ($dataObjects as $key => $data) {
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
							//sleep(5);				//usually the weird character is an empty field...TODO
						}
					}
				}

			}

	}

	public function minimalActivity($S, $R) {

		//method called by ProcessFiles

		echo "\e[36mFin Record Class ID: " . $S->record_id . "\e[m\t";
		echo "Company: " . $R->company_name . "\t" . "\e[31mcharacter count: \e[m" . $S->char_id . "\n";
	}

	public function displayActivity($c, $S, $R, $B) {

		echo "\e[10;20H";
		echo "Company: " . $R->company_name . "\t";
		echo "CIK: " . $R->cik . "\t";
		echo "\e[33mKey Name: " . $R->key_name . "\e[m\t";
		echo "Key Value: " . $R->key_value . "\n\n";
		echo "\e[14;20H";

		echo "\e[35mDatastore count: " . count($R->dataStore) . "\e[m\t";
		echo "Current Data ID: " . $R->currentObjectId . "\t";
		if (count($R->dataStore) > 0) {
			$data_id = $R->currentObjectId;
			$data = $R->dataStore[$data_id];
			$type = $data->data_type;
			echo "Data Taxonomy: " . $data->taxonomy . "\t";
			echo "Data Object Type: " . "\e[31m" . $type . "\t" . "\e[m";		//resets default;
			echo "Data Units Type: " . $data->data_units . "\n";
			echo "Data Unit Flag: " . "\e[31m" . $B->unit_flag . "\e[m" . "\n";
			echo "Data Label Value: " . $data->label . "\n";
			echo "Data Description: " . $data->description . "\n";  //this is really long string so separate line
			echo "\e[31mData Complete Status: " . $data->completion_status . "\e[m\n";
			if($data->completion_status > 6){echo "COMPLETE TOO HIGH!\n"; sleep(10);}

			if (count($data->entryStore) > 0) {
				echo "Recordstore count: " . count($data->entryStore) . "\t";
				echo "\e[36mCurrent Entry ID: " . $data->currentEntryId . "\e[m\t";
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

		echo "\e[31mcharacter count: \e[m" . $S->char_id . "\t";
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

		$FilePace = function ($R, $S, $position,$c) {
			if ($S->char_id > $position) {
				if ($c == '}' || $c == '{') {
					sleep(self::BRACE_TIME);
				} elseif ($c == ']' && $S->quotes != 1) {
				//	$theD($R, $S);						//dont need a moment
					sleep(self::BRACKET_TIME);
				} else {
					sleep(self::CHAR_TIME);
				}
			} else {
				sleep(0);
			}
		};

		if (preg_match("/^Tesla.*$/",$R->company_name)) {
			$FilePace($R, $S, 2210975, $c);
		} elseif(preg_match("/^Carnival.*$/",$R->company_name)) {
			$FilePace($R, $S, 3100000, $c);
 		} elseif(preg_match("/^RANCON.*$/",$R->company_name)) {
			$FilePace($R, $S, 3100000, $c);
		} elseif(preg_match("/^1841661.*$/",$R->cik)) {
			$FilePace($R, $S, 0, $c);
		}else {
			$FilePace($R,$S,0,$c);
		}
	}
}