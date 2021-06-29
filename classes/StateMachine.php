<?php

namespace JDApp;
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

	public function handleRightBrace(): int {

			if($this->quotes == 2 && $this->bracket == 1 && $this->task == DONE) {     //parameters at end of an entry
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
				array_push($this->history, MAKEKEY);
				$result = GETNEXTCHAR;
			} elseif ($this->colon == 1) {
				$this->braces += 1;
				$this->colon = ZERO;
				$this->char_id += 1;
				$this->task = STOREKEY;
				array_push($this->history, STOREKEY);
				$result = CONTINUEPROCESSING;
			} elseif ($this->quotes == 1) {
				$this->char_id += 1;
				$result = GETNEXTCHAR;
			}

			return $result;

	}
	public function handleBackSlash(): int {
		//inside labels and descriptions sometimes the escape \ is used on quotes etc

		if ($this->quotes == 1 ) {
			$this->char_id += 1;
			$this->backslash += 1;
		}
		return CONTINUEPROCESSING;
	}

	public function handleQuotes (): int {

		$result = GETNEXTCHAR;

		if($this->quotes == ZERO && $this->task == MAKEKEY) {							//OK
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
				array_push($this->history, DONE);
			}
		}

		return $result;
	}

	public function handleDefault(): int {

			switch ($this->key) {															//was $this->task
				case SWDEFAULT: $result = CONTINUEPROCESSING; break;
				default: $result = CONTINUEPROCESSING;							//ok
			}
			$this->char_id += 1;
			return $result;
	}

	public function handleColon(): int {

		$result = GETNEXTCHAR;

		if ($this->task == DONE) {
			//ignore if inside quotes
			if ($this->backslash == 1) {                                        //ok
				$this->char_id += 1;
				$this->backslash = ZERO;
			} else {
				$this->task = MAKEVALUE;                                        //ok
				array_push($this->history, MAKEVALUE);
				$this->quotes = ZERO;
				$this->colon += 1;
				$this->char_id += 1;
			}
		} else {
			$this->task = MAKEVALUE;
			array_push($this->history, MAKEVALUE);
			$this->quotes = ZERO;
			$this->colon += 1;
			$this->char_id += 1;
		}

		return $result;
	}

	public function handleComma(): int {

		$result = CONTINUEPROCESSING;
		if($this->entry_flag == 0 && $this->task != MAKEKEY)  {
			switch ($this->quotes) {                                                //ok
				case 2:
					$this->quotes = ZERO; $this->task = STOREKEYVAL; array_push($this->history, STOREKEYVAL);
					break;
				case 0:
					$this->task = STOREKEYVAL; 	array_push($this->history, STOREKEYVAL);
					break;
				case 1:
					break;
				default:
			}
		} elseif ($this->entry_flag == 1) {										//ok. this is behavior if at end of entry
			$this->quotes = ZERO;
			$this->entry_flag = 0;
			$this->task = CREATEENTRY; array_push($this->history, CREATEENTRY);
		} elseif ($this->task == MAKEKEY) {
			$result = GETNEXTCHAR;
		}

		$this->colon = ZERO;
		$this->char_id += 1;
		return $result;
	}

	public function handleLeftBracket():int {
		if($this->quotes == 1) {
			$this->char_id += 1;
			$result = CONTINUEPROCESSING;
		} else {
			$result = CONTINUEPROCESSING;
			$this->task = CREATEENTRY;
			$this->colon = ZERO;
			array_push($this->history, CREATEENTRY);
			$this->datatype = ENTRY;								//processHEADER, processDATA, processENTRY
			$this->char_id += 1;
			$this->bracket += 1;
		}
		return $result;
	}

	public function handleRightBracket(): int {				//HANDLES COMMA DUTIES if no comma after right brace

		$result = GETNEXTCHAR;

		if ($this->entry_flag == 1 ) {                                        //ok. this is behavior if at end of entry and no comma
			$this->quotes = ZERO;
			$this->entry_flag = 0;
			$result = GETNEXTCHAR;
			$this->bracket -= 1;
			$this->datatype = DATA;						//time to go back to data level
			$this->task = MAKEKEY;						//pretty much the default task
		} elseif($this->quotes == 1) {					//if inside a quoted value string
			$result = CONTINUEPROCESSING;
		}

		$this->char_id += 1;
		return $result;

	}


} //end of class


