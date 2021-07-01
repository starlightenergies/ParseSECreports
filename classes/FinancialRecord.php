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

/**
 * @purpose:    	XBRL Report Processing Application
 * @filename:    	FinancialRecord.php
 * @version:    	1.0
 * @lastUpdate:  	2021-06-28
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

		//six steps to create object
		//create, store, get datatype, get entries, get label, get description
		$dataObject = new Data($taxo);
		$status = $dataObject->updateCompletionStatus();			//taxonomy property updated 1/6
		$object_id = $dataObject->getId();
		$this->dataStore[$object_id] = $dataObject;
		$this->currentObjectId = $object_id;
		$status = $dataObject->updateCompletionStatus();			//curr_id property updated 2/6
	}
}

