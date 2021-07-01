<?php
namespace JDApp;
require "vendor/autoload.php";
require_once "includes/programDefines.inc";
use JDApp\FinancialRecord as Record;
use JDApp\StateMachine as State;
use JDApp\RecordBuilder as Builder;
use JDApp\Activity;

/*
MIT LICENSE
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
 * @filename:    	ProcessFiles.php
 * @version:    	1.1
 * @lastUpdate:  	2021-06-29
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


class ProcessFiles {

	public string $currentFile;
	public object $Record;
	public object $State;
	public object $Label;
	public object $Builder;
	public object $Activity;

	public function __construct($file) {
		$this->currentFile = $file;
		$this->Record = new Record();
		$this->State = new State();
		$this->State->record_id = Record::$record_id;
		$this->Label = new Labels(HEADER, $this->Record, $this->State);
		$this->Builder = new Builder($this->State);
		$this->Activity = new Activity($this->Record);			//NEW TODO may need to setup object in reportproc. and terms too.
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
				$result = $StateM->handleRightBrace($Recd,$Build);
				if ($result == 1 && $StateM->datatype == ENTRY) {
					$Build->processEntry($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == HEADER) {
					$Build->processHeader($Recd, $c);
				}
				break;
			case '"':
				$StateM->key = DOUBLEQUOTE;
				$result = $StateM->handleQuotes();                    //always returns a GETNEXTCHAR

				break;
			case ':':
				$StateM->key = COLON;
				$result = $StateM->handleColon();
				if ($result == 1 && $StateM->datatype == DATA) {
					$Build->processData($Recd, $c);
				}
				break;
			case ',':
				$StateM->key = COMMA;
				$result = $StateM->handleComma($Build);
				if ($result == 1 && $StateM->datatype == HEADER) {
					$Build->processHeader($Recd, $c);
				} elseif ($result == 1 && $StateM->datatype == DATA) {
					$result2 = $Build->processData($Recd, $c);			//checking if need to build new data object
					if($result2 == 1) {
						//go back again and build object
						$Build->processHeader($Recd, $c);				//builds new datatype
					}
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
			case '\\':
				$StateM->key = BACKSLASH;
				$result = $StateM->handleBackSlash();
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

		$this->Activity->displayActivity($c, $StateM, $Recd,$Build);
		return $result;
	}

}
