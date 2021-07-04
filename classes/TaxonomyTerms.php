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
 * @filename:    	TaxonomyTerms.php
 * @version:    	1.50
 * @lastUpdate:  	2021-07-02
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:    		2021-06-24
 * @controller:
 * @view:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comments:		Terms are scattered throughout JSON. Terms are a moving target and most data types are of little value.
 */


class TaxonomyTerms {

	public array $header_terms = [];
	public array $data_type_terms = [];
	public array $entry_type_terms = [];
	public array $units_type_terms = [];
	public string $CompanyCIK = '';
	public string $sourceFile;

	public function __construct (int $cik) {
		$this->CompanyCIK = $cik;
		$this->sourceFile = __DIR__ . '/../includes/taxonomy_terms.txt';

		//load terms arrays from file, only using two arrays at the moment TODO
		$theArrays = $this->loadTerms();
		//create data types
		$this->header_terms = $theArrays[0];
		$this->data_type_terms = $theArrays[1];
		$this->units_type_terms = $theArrays[2];
	}

	public function deleteTerm($term,$cik) {

		$old_term = $term;
		//delete term from array
	}

	public function editTerm($term,$cik) {

		$new_term = $term;
		//edit existing term in array
	}

	public function addDataTerm($term) {

		$this->data_type_terms[] = $term;
		if(in_array($term,	$this->data_type_terms)) {
			echo NEWTERM . $term . ADDEDTODB;
		}

	}

	public function addHeaderTerm($term) {

		$this->header_terms[] = $term;
		if(in_array($term,	$this->header_terms)) {
			echo NEWTERM . $term . ADDEDTODB;
			//should log these kinds of things too TODO
		}
	}


	public function addMeasureTerm($term) {

		$this->units_type_terms[] = $term;
		if(in_array($term,	$this->units_type_terms)) {
			echo NEWTERM . $term . ADDEDTODB;
			//should log these kinds of things too TODO
		}
	}

	public function getDataTerms(): array {
		return $this->data_type_terms;
	}

	public function getHeaderTerms(): array {
		return $this->header_terms;
	}


	public function getMeasureTerms(): array {

		return $this->units_type_terms;
	}


	public function loadTerms(): array {

		//testing file is readtaxodata.php its the source for below
		$file = $this->sourceFile;
		$termsFile = new \SplFileObject($file, 'r');

		$header = [];
		$measure = [];						//units of measure, (USD, EUR, shares etc)
		$data = [];
		$datatype = [];
		$flag = 0;
		$comment = '^#.*';
		$empty = '^$';

		while (!$termsFile->eof()) {
			//skip comment and empty lines
			$line = $termsFile->fgets();
			if (preg_match("/$comment/", $line) || preg_match("/$empty/", $line)) {
				continue;
			}

			//set flag for now only using header and datatype. will expand this later TODO
			if (preg_match("/^\[/", $line)) {
				switch (trim($line)) {
					case '[header]':
						$flag = 1;
						break;
					case '[measurement]':
						$flag = 2;
						break;
					case '[data]':
						$flag = 4;
						break;
					case '[datatype]':
						$flag = 4;
						break;
					default:
				}

			} else {
				//store line in correct array
				switch ($flag) {
					case '1':
						$info = trim($line);
						array_push($header,$info);
						break;
					case '2':
						$info = trim($line);
						array_push($measure,$info);
						break;
					case '3':
						$info = trim($line);
						array_push($data,$info);
						break;
					case '4':
						$info = trim($line);
						array_push($datatype,$info);
						break;
					default:
				}
			}
		}

//package arrays for return only two used at moment
		$package = [];
		array_push($package, $header);
		array_push($package, $datatype);
		array_push($package, $measure);
		return $package;

	}

	public function updateTermsDatabase(): bool {

		$file = $this->sourceFile;
		$fileProc = new \SplFileObject($file, 'w');
		// Return date/time info of a timestamp; then format the output
		$mydate = getdate(date("U"));
		$mydate = $mydate['weekday'] . ", " . $mydate['month'] . " " . $mydate['mday'] . ", " . $mydate['year'];
		$blank_line = "\n";

		//terms currently in use
		$terms = $this->header_terms;
		$UOM = $this->units_type_terms;
		$data = $this->data_type_terms;

		//sort them
		asort($terms);

		//file header
		$dataH[1] = "#total headers: " . count($terms) . " total data types: " . count($data) . "\n";
		$dataH[2] = "#last update: " . $mydate . "\n";
		$dataH[3] = "#could use identifier re taxonomy type            TODO\n";
		$dataH[4] = "#could add qualitative markers here               TODO\n";

		//truncate and update file
		foreach ($dataH as $item) {
			$fileProc->fwrite($item);
		}
		$fileProc->fwrite($blank_line);
		$fileProc->fwrite($blank_line);
		$fileProc->fwrite("[header]\n");

		foreach ($terms as $item) {
			$item .= "\n";
			$fileProc->fwrite($item);
		}

		$fileProc->fwrite($blank_line);
		$fileProc->fwrite($blank_line);
		$fileProc->fwrite("[data]\n");

		$fileProc->fwrite($blank_line);
		$fileProc->fwrite($blank_line);
		$fileProc->fwrite("[measurement]\n");

		foreach ($UOM as $item) {
			$item .= "\n";
			$fileProc->fwrite($item);
		}

		$fileProc->fwrite($blank_line);
		$fileProc->fwrite($blank_line);
		$fileProc->fwrite("[datatype]\n");

		foreach ($data as $item) {
			$item .= "\n";
			$fileProc->fwrite($item);
		}


		//check if written
		if(file_exists($file)) {return true;}

		return false;
	}


} //end of class