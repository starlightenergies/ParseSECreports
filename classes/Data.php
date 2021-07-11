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
 * @filename:    	Data.php
 * @version:    	1.00
 * @lastUpdate:  	2021-06-28
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:		Container
 * @since:    		2021-06-24
 * @controller:
 * @view:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment: 		Data stores Entries by data type for storage in Record class store
 */

/* usage
	- holds all data records and associated entries in an SEC companyfacts CIK numbered JSON file in data dir
	- persistent storage in stockengine database. linked to financialrecords table, company table
	- used in processing by ReportProcessor.php, which transfers json data into data object and stores entries in entry store
	-  data objects held in financialrecords object. these data objects hold all the entries from entries object
*/


class Data {

	public int $id;
	public string $taxonomy;
	public static int $data_id = 1;
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
		$this->id = DATA::$data_id++;
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

		// when this reaches 6 then a new data object can be started
		//taxonomy, type, label, description, entries, object
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