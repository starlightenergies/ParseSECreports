<?php

namespace JDApp;
use Hamcrest\Text\IsEmptyStringTest;

require_once "includes/programDefines.inc";


class StateMachine
{

	//state machine properties
	public int $braces;                                //counts left and right braces. determines if to make values an array
	public int $colon;
	public int $bracket;
	public int $entry_flag;								//if one, then need entry creation
	public string $task;
	public string $status;
	public string $datatype;                            	//header or data or entry
	public int $char_id;
	public int $quotes;
	public int $backslash;
	public int $key;
	public int $record_id;
	public array $history = [];							//keeps track of history

	public function __construct()
	{

		$this->task = 'start';
		$this->datatype = 'header';
		$this->status = "in-process";
		$this->braces = 0;
		$this->char_id = 0;
		$this->quotes = 0;
		$this->key = 0;
		$this->backslash = 0;
		$this->colon = 0;
		$this->bracket = 0;
		$this->entry_flag = 0;


	}

	public function statusMessage()
	{

		echo "Fin Record Class ID: " . $this->record_id . "\t";
		echo "Previous Task: " . $this->task . "\t";
		if ($this->datatype == HEADER) {
			echo "Working on Header\n";
		} elseif ($this->datatype == DATA) {
			echo "Working on Data\n";
		} else {
			echo "Working on Entry\n";
		}
	}

	//establish what task we are working on (make key, make value, make array)
	//then evaluate what type of key etc (eg keep track of state)

	public function handleRightBrace($R,$B): int {
			//$R is the Record object, $B the build object

			if($this->quotes == 2 && $this->bracket == 1 && $this->task == DONE) {     //parameters only at end of an entry
				$this->braces -= 1;
				$this->entry_flag += 1;												//flag that the comma can catch below and create entry
				$this->task = STOREKEYVAL;
				$result = CONTINUEPROCESSING;
			} elseif ($this->quotes == 1) {
				$result = CONTINUEPROCESSING;
			} else {
				$this->braces -= 1;													//keep accurate count just in case
				$result = GETNEXTCHAR;
			}

			//test to see if completion status should be updated
			$previousCharacter = array_pop($this->history);
			//right bracket is sign that all entries are done. but sometimes a comma follows right bracket so cant do too early
			if($previousCharacter == RIGHTBRACKET) {
				$curr_data = $R->dataStore[$R->currentObjectId];
				$curr_data->updateCompletionStatus();

				if ($curr_data->completion_status == 6) {
					$this->datatype = HEADER;
					$this->task = MAKENEWDATAOBJECT;
					$B->data_type_flag = 1;            //its almost always the first to be created after obj created
					$result = CONTINUEPROCESSING;        //causes return to build in Header Loop below...

				} else {
					$result = GETNEXTCHAR;								//same as else above
				}
			}
			array_push($this->history, RIGHTBRACE);								//history accessible to next character
			$this->char_id += 1;
			return $result;
	}

	public function handleLeftBrace(): int {

			$result = GETNEXTCHAR;
			//dont count braces inside values   TODO

			if ($this->quotes == ZERO && $this->colon != 1)  {
				$this->braces += 1;
				$this->char_id += 1;
				$this->task = MAKEKEY;
				$result = GETNEXTCHAR;
			} elseif ($this->colon == 1) {
				$this->braces += 1;
				$this->colon = ZERO;
				$this->char_id += 1;
				$this->task = STOREKEY;
				$result = CONTINUEPROCESSING;
			} elseif ($this->quotes == 1) {
				$this->char_id += 1;
				$result = GETNEXTCHAR;
			}
			array_push($this->history, LEFTBRACE);								//history accessible to next character
			return $result;

	}
	public function handleBackSlash(): int {
		//inside labels and descriptions sometimes the escape \ is used on quotes etc

		if ($this->quotes == 1 ) {
			$this->char_id += 1;
			$this->backslash += 1;
		}

		array_push($this->history, BACKSLASH);								//history accessible to next character
		return CONTINUEPROCESSING;
	}

	public function handleQuotes (): int {

		$result = GETNEXTCHAR;

		if($this->quotes == ZERO && $this->task == MAKEKEY) {                            //OK
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif($this->quotes == ZERO && $this->task == MAKETAXONOMYKEY) {							//OK
				$this->quotes += 1;
				$this->char_id += 1;
		} elseif($this->quotes == ZERO && $this->task == MAKEVALUE) {					//ok
			$this->colon = ZERO;
			$this->quotes += 1;
			$this->char_id += 1;
		} else {
			//ignore quotes inside values
			if($this->backslash == 1) {										//ok
				$this->char_id += 1;
				$this->backslash = 0;
			} else {														//ok
				$this->quotes += 1;
				$this->task = DONE;
				$this->char_id += 1;
			}
		}
		array_push($this->history, DOUBLEQUOTE);								//history accessible to next character
		return $result;
	}

	public function handleDefault(): int {

			switch ($this->key) {												//was $this->task
				case SWDEFAULT: $result = CONTINUEPROCESSING; break;
				default: $result = CONTINUEPROCESSING;
			}
			$this->char_id += 1;
			return $result;
	}

	public function handleColon(): int {

		//ignore if inside quotes
		$result = GETNEXTCHAR;
		if ($this->task == DONE) {
			if ($this->backslash >= 1) {
				$this->backslash = ZERO;
			} elseif ($this->datatype == DATA) {
				$this->task = TESTKEY;
				$this->quotes = ZERO;
				$this->colon += 1;
				$result = CONTINUEPROCESSING;
			} else {
				$this->task = MAKEVALUE;
				$this->quotes = ZERO;
				$this->colon += 1;
			}
		} else {
			if($this->quotes == 1) {
				$result = GETNEXTCHAR;
			} else {
				//default action for a colon
				$this->task = MAKEVALUE;
				$this->quotes = ZERO;
				$this->colon += 1;
			}
		}
		$this->char_id += 1;
		array_push($this->history, COLON);								//history accessible to next character
		return $result;
	}

	public function handleComma($B): int {

		//$R is the current record builder object, gives access to unit_flag
		$result = CONTINUEPROCESSING;
		//get previous character from history
		$previousCharacter = array_pop($this->history);					//TODO may not want to pop this, just read

		if($this->entry_flag == 0 && $this->task != MAKEKEY)  {
			switch ($this->quotes) {
				case 2: $this->quotes = ZERO; $this->task = STOREKEYVAL; break;
				case 0: $this->task = STOREKEYVAL; break;
				case 1: break;
				default:
			}
		} elseif ($this->entry_flag == 1) {                                        //ok. this is behavior if at end of entry
			$this->quotes = ZERO;
			$this->entry_flag = 0;
			$this->task = CREATEENTRY;
		} elseif($this->task == MAKEKEY && $previousCharacter == RIGHTBRACKET) {		//testkey method captures unit_flag == 1 situations
		//	echo "in the correct comma loop\n";
		//	sleep(5);
			$B->unit_flag = 1;															//there are special cases where data types have > one unit type
			$result = GETNEXTCHAR;
		} elseif ($this->task == MAKEKEY) {
			$result = GETNEXTCHAR;
		}

	//	echo "previous character: " . $previousCharacter . "\n";
		array_push($this->history, COMMA);									//history accessible to next character
		$this->colon = ZERO;
		$this->char_id += 1;
		return $result;
	}

	public function handleLeftBracket():int {
		if($this->quotes == 1) {
			$result = CONTINUEPROCESSING;
		} else {
			$result = CONTINUEPROCESSING;
			$this->task = CREATEENTRY;
			$this->colon = ZERO;
			$this->datatype = ENTRY;								//processHEADER, processDATA, processENTRY
			$this->bracket += 1;
		}
		array_push($this->history, LEFTBRACKET);								//history accessible to next character
		$this->char_id += 1;
		return $result;
	}

	public function handleRightBracket(): int {							//HANDLES COMMA DUTIES if no comma after right brace

		$result = GETNEXTCHAR;

		if ($this->entry_flag == 1 ) {                                        //ok. this is behavior if at end of entry and no comma
			$this->quotes = ZERO;
			$this->entry_flag = 0;
			$this->bracket -= 1;
			$this->datatype = DATA;											//time to go back to data level
			$this->task = MAKEKEY;											//pretty much the default task
		} elseif($this->quotes == 1) {										//if inside a quoted value string
			$result = CONTINUEPROCESSING;
		}
		array_push($this->history, RIGHTBRACKET);								//history accessible to next character
		$this->char_id += 1;
		return $result;

	}


} //end of class


