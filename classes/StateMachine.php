<?php

namespace JDApp;
require_once "includes/programDefines.inc";


class StateMachine
{

	//state machine properties
	public int $braces;                                //counts left and right braces. determines if to make values an array
	public string $task;
	public string $status;
	public string $datatype;                            	//header or data or entry
	public int $char_id;
	public int $quotes;
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


	}

	public function statusMessage()
	{

		echo "Fin Record Class ID: " . $this->record_id . "\t";
		echo "Current Task: " . $this->task . "\t";
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
			$result = 1;
			if($this->braces == 5) {
				$this->task = STOREKEYVAL;
				$this->braces += 1;
				$result = CONTINUEPROCESSING;
			} elseif($this->task == MAKELABEL) {
				$this->braces -= 1;
				$this->quotes = ZERO;
				$result = GETNEXTCHAR;
			} elseif($this->task == MAKEDESCRIPTIONVALUE) {
				$this->braces -= 1;
				$this->quotes = ZERO;
				$this->datatype = HEADER;
				$result = GETNEXTCHAR;
			} elseif ($this->quotes == 1){
				$result = CONTINUEPROCESSING;
			}
			$this->char_id += 1;
			return $result;
	}

	public function handleLeftBrace(): int {

			//initial character received
			if ($this->char_id == ZERO && $this->braces == ZERO) {
				$this->braces += 1;
				$this->char_id += 1;
				$this->task = MAKEKEY;
				array_push($this->history, MAKEKEY);
			} elseif ($this->braces > ZERO && $this->quotes != 1) {
				$this->braces += 1;
				$this->char_id += 1;
				$this->task = STOREKEY;
				array_push($this->history, STOREKEY);
				$this->quotes = ZERO;
			} elseif ($this->quotes == 1) {
				$this->char_id += 1;

			}

			return CONTINUEPROCESSING;

	}

	public function handleQuotes (): int {

		if($this->quotes == ZERO && $this->task == MAKEKEY) {
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif($this->quotes == ZERO && $this->task == MAKEVALUE) {
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif ($this->quotes == ZERO && $this->task == STOREKEYVAL) {
			$this->quotes += 1;
			$this->char_id += 1;
			$this->task = MAKEKEY;
			array_push($this->history,MAKEKEY);
		} elseif ($this->quotes == ZERO && $this->task == MAKELABEL) {
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif ($this->quotes == ZERO && $this->task == MAKELABELVALUE) {
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif ($this->quotes == ZERO && $this->task == MAKEDESCRIPTION) {
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif ($this->quotes == ZERO && $this->task == MAKEDESCRIPTIONVALUE) {
			$this->quotes += 1;
			$this->char_id += 1;
		} elseif ($this->quotes == ZERO && $this->task == CREATEDATAOBJ) {
			$this->quotes += 1;
			$this->char_id += 1;
			$this->task = MAKEKEY;
			array_push($this->history,MAKEKEY);

			} elseif($this->quotes == 1) {
			//this assumes there are no quotes in the value portion TODO
			$this->quotes += 1;
			$this->task = DONE;
			$this->char_id += 1;
			array_push($this->history,DONE);
		}

		return GETNEXTCHAR;
	}

	public function handleDefault(): int {

			switch($this->task) {
				case MAKEDESCRIPTION;
				case MAKELABEL:
				case IGNORE: $result = GETNEXTCHAR;break;
				case SWDEFAULT: $result = CONTINUEPROCESSING; break;
				default: $result = CONTINUEPROCESSING;
			}
			$this->char_id += 1;
			return $result;
	}

	public function handleColon(): int {
			//this assumes there are no colons in the value portion TODO
		if($this->task == MAKELABEL) {
			$this->task = MAKELABELVALUE;
			array_push($this->history, MAKELABELVALUE);
		} elseif ($this->task == MAKEDESCRIPTION) {
			$this->task = MAKEDESCRIPTIONVALUE;
			array_push($this->history,MAKEDESCRIPTIONVALUE);
		} else {
			$this->task = MAKEVALUE;
			array_push($this->history, MAKEVALUE);
		}

		$this->quotes = ZERO;
		$this->char_id += 1;
		return GETNEXTCHAR;
	}

	public function handleComma(): int {

		$result = CONTINUEPROCESSING;

		if($this->braces == 6) {
			$this->task = CREATEENTRY;
			$this->braces = 4;
		} elseif ($this->task == MAKELABEL) {
			$result = GETNEXTCHAR;
			$this->datatype = DATA;
		} elseif ($this->task == MAKELABELVALUE && $this->quotes == 2) {
				$this->task = MAKEDESCRIPTION;
				$this->quotes = ZERO;
				$result = GETNEXTCHAR;
		} else {
			switch ($this->quotes) {
				case 2: $this->quotes = ZERO; $this->task = STOREKEYVAL; array_push($this->history, STOREKEYVAL);break;
				case 0: $this->task = STOREKEYVAL; array_push($this->history, STOREKEYVAL); break;
				case 1: break;
				default:
			}
		}
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
			array_push($this->history, CREATEENTRY);
			$this->datatype = ENTRY;
			$this->char_id += 1;
		}
		return $result;
	}

	public function handleRightBracket(): int {
		if($this->quotes == 1) {
			$this->char_id += 1;
			$result = CONTINUEPROCESSING;
		} else {
			$this->braces = 4;
			$this->task = MAKELABEL;
			$this->quotes = ZERO;
			$result = GETNEXTCHAR;
		}
		return $result;

	}


} //end of class


