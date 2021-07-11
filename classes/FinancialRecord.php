<?php
namespace JDApp;


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

use Nette\Database\ResultSet;

/**
 * @purpose:    	XBRL Report Processing Application
 * @filename:    	FinancialRecord.php
 * @version:    	1.0
 * @lastUpdate:  	2021-07-09
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:    		2021-06-24
 * @controller:
 * @view:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment: 		Record stores Data/entries in Datastore for a single JSON file from a company.
 */

/* usage
	- holds all data in an SEC companyfacts CIK numbered JSON file in data dir
	- persistent storage in stockengine database in the financialrecords table
	- used in processing by ReportProcessor.php, which transfers json data into
	- this record, data objects in this datastore. data objects hold all the entries
*/


class FinancialRecord {

	//class property
	public static int $record_id = 0;

	//header properties
	public int $id;
	public string $company_name;
	public int $cik;                                //company index key (S.E.C.)
	public array $header;                                // array [key] = value
	public string $created_at;                            //timestamp
	public string $key_name;
	public string $key_value;
	public int $storedInDb;


	//data properties
	public array $dataStore;                            //array of data record batches
	public int $currentObjectId;                        //data object currently being worked on


	public function __construct() {

		$this->id = self::$record_id++;
		$this->key_name = '';
		$this->key_value = '';
		$this->dataStore = [];
		$this->currentObjectId = 0;
		$this->cik = 0;
		$this->company_name = '';
		$this->storedInDb = 0;
	}


	public function createDataObject($taxo) {

		//six steps to create object
		//create, store, get datatype, get entries, get label, get description
		$dataObject = new Data($taxo);
		$status = $dataObject->updateCompletionStatus();            //taxonomy property updated 1/6
		$object_id = $dataObject->getId();
		$this->dataStore[$object_id] = $dataObject;
		$this->currentObjectId = $object_id;
		$status = $dataObject->updateCompletionStatus();            //curr_id property updated 2/6
	}

	public function storeRecordData(JDdatabase $dbh, array $stocks): int {

		//method called by Report Processor after file is completely processed
		//cascading effect, as this method will call each dataobject in the store
		//to store its details, and that method will call each entry in its entrystore
		//to store entry data into the db

		//default
		$insertID = 0;

		//list variables to be used or stored in the database.
		$rec_cik = $this->cik;
		$rec_id = $this->id;
		$store_count = count($this->dataStore);

		//database entries start here.
		//$stocks are created above as array of Resultset from calling DB,
		foreach ($stocks as $key => $rowObject) {
			if ($rec_cik == $rowObject->cik) {
				echo "Array key: " . $key . "\n";
				echo "record match record CIK: " . $rec_cik . " and db call CIK: " . $rowObject->cik . "\n";
				$co_id = intval($rowObject->id);
				echo "company id in DB: " . $co_id . "\n";
				$insertID = $dbh->enterRecordinfo($co_id, $rec_id, $store_count);
				echo "insertID in FinRecord Class (1st Attempt): " . $insertID . "\n";
				sleep(5);
				$this->storedInDb = 1;
				return $insertID;
			}
		}

		return $insertID;
	}

}

