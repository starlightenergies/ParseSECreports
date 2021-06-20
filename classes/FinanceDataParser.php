<?php
namespace JDApp;

/**
 * @purpose:	Stores financial data into mysql database
 * @filename:	FinanceDataParser.php
 * @version:  	1.0
 * @lastUpdate: 2021-06-13
 * @author:    	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:   	2021-06-13
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:
 */

//The SEC gets around 14000 reports per day. They
//can be downloaded in various formats, including
//JSON, which is the format used here.

//Because the data is quite variable, and its an object
//mixed with stdClass objects, arrays and strings without
//an warning as to where, I have produced this parser
//to relieve pain from the above

class FinanceDataParser {

	public $jsonfile;
	public $charArray;

	public function __construct (string $jsonfile) {
		$this->jsonfile = $jsonfile;
	}

	public function createArray (): array {
		$file = [];
		$this->charArray = str_split($this->jsonfile);
		return $this->charArray;
	}

	public function swapBrackets (): array {

		$newarray = [];
		$file = $this->charArray;
		foreach ($file as $char) {
			switch($char) {
				case '{': $char = '[';break;
				case '}': $char = ']';break;
			}
			$newarray[] = $char;
		}

		return $newarray;
	}

	public function createFile() {

		$fh =fopen('tempfile.php', 'w');
		fwrite($fh, "<?php\n\n");
		fwrite($fh,'$myval = array();');
		fwrite($fh, "\n");
		fwrite($fh, 'return $myval = ');
		//create an array in the file.
		$data = $this->swapBrackets();

		foreach ($data as $char) {
			if($char == ":") {
				fwrite($fh, "=>");
			} elseif ($char == "]") {
				fwrite($fh, "\n");
			} else {
				fwrite($fh, $char);
			}
		}

		fclose($fh);
	}

}