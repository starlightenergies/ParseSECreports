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
 * @filename:    	Entry.php
 * @version:    	1.0
 * @lastUpdate:  	2021-06-28
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:		Container
 * @since:    		2021-06-24
 * @controller:
 * @view:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment: 		Entry saves entries for storage in Data class store
 */

/* usage
	- holds all entries in an SEC companyfacts CIK numbered JSON file in data dir
	- persistent storage in stockengine database. linked to data table, company table. taxonomy terms table
	- used in processing by ReportProcessor.php, which puts json data into data object and stores entries in entry store
	-  data objects held in financialrecords object. these data objects hold all the entries from these entries objects
*/

class Entry {

	public int $id;
	public static int $entry_id = 1;
	public string $name;
	public array $keys = [];
	public array $values = [];
	public string $current_key;
	public string $current_value;
	public string $status;

	public function __construct($name) {
		$this->id = Entry::$entry_id++;
		$this->name = $name;
		$this->current_key = '';
		$this->current_value = '';
		$this->status = 'open';
	}

	public function getId(): int {
		return $this->id;
	}

	public function insertKey(): int {
		if ($this->status == 'open') {
			$this->keys[] = $this->current_key;
			//check if insert succeeded
			if (count($this->keys) > 0) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function insertValue(): int {
		$key = $this->current_key;
		$value = $this->current_value;
		$this->values[$key] = $value;

		//check for insert success
		if(count($this->values) > 0 ) {
			$this->current_key = '';
			$this->current_value = '';
			return 1;
		} else {
			return 0;
		}
	}
}