<?php

namespace JDApp;


/**
*
* provides ansi tools for terminal control of cursor, colors etc
*
* @filename		AnsiTests.php
* @version  	1.0
* @lastUpdate  	2021-07-02
* @author    	James Danforth <james@reemotex.com>
* @category
* @feature   	ControlTerminal
* @pattern		Class
* @since   		2021-03-19
* @status
* @flowchart
* @controller
* @license
* @delegates
* @comment    	provides terminal control methods..
*/


class AnsiTests {


	public int $rows;
	public int $cols;

	public function __construct() {

		$this->rows = exec("tput lines");
		$this->cols = exec("tput cols");

	}


	public function jdsleep($int) {

		$rows = exec("tput lines");
		$cols = exec("tput cols");
		echo "\e[?25l"; //hides the cursor
		echo "\e[s";    //saves cursor position

		while ($int > 0) {
			echo "\e[{$rows};{$cols}H";	//go to specific position on screen
			if ($int <= 9) {
			echo "\e[K";  	//clears line from cursor to last column
          echo "\e[1C";		//goes backward 1 column
      	}
	  	echo $int;
	  	echo "\e[2D";		//goes forward 2 columns
	  	$int--;
	  	sleep(1);

    	}

		echo "\e[2D";
		echo "\e[K";
		echo "\e[u"; 			//restores saved cursor position
		echo "\e[?25h";			//unhides the cursor

	}



	public function clearscreen() {
		echo "\e[2J";		//clears screen
		echo "\e[0m";		//resets default
	}


	public function drawHorizBorder($c) {

		$count=0;
		while ($c > $count) {
			echo " ";
			$count++;
		}
	}

	public function drawLeftVertBorder($r,$c=0) {

		$count=0;
		while ($r > $count) {
			echo "\e[{$count};{$c}H";
			echo "  ";
			$count++;
		}


	}


	public function drawRightVertBorder($r,$c) {

		$c = $c - 1;
		$count=0;
		while ($r > $count) {
			echo "\e[{$count};{$c}H";
			echo "  ";
			$count++;
		}


	}


	public function drawBoxBorder($rows=40,$cols=140,$color) {

		$rows = $this->rows;
		$cols = $this->cols;


		echo "\e[0;0H";
		echo "\e[{$color}m";
		$this->drawHorizBorder($cols);
		echo "\e[{$rows};0H";
		$this->drawHorizBorder($cols);

		$this->drawLeftVertBorder($rows);
		$this->drawRightVertBorder($rows,$cols);


	}

	public function centeredBoxBorder($color, $msg) {

		$rows = $this->rows;
		$cols = $this->cols;

		echo "\e[2J";		//clear screen
		$l = 60;
		$h = 20;
		$upperLeftX = round(($rows-$h)/2);
		$lowerLeftX = $upperLeftX + $h;
		$upperLeftY = round(($cols - $l)/2);
		$upperRightX = $upperLeftX;
		$upperRightY = $upperLeftY + $l;

		$this->clearscreen();
		echo "\e[{$upperLeftX};{$upperLeftY}H";
		echo "\e[{$color}m";
		$this->drawHorizBorder($l);

		echo "\e[{$lowerLeftX};{$upperLeftY}H";
		$this->drawHorizBorder($l);

		//get first message coordinate
		$messageStartX = $upperLeftX + 5;

		//draw left vertical
		echo "\e[{$upperLeftX};{$upperLeftY}H";
		$count=0;
		while ($h > $count) {
			echo "  ";
			$count++;
			$x = $upperLeftX + $count;
			echo "\e[{$x};{$upperLeftY}H";
		}

		//get second message coordinate
		$messageStartY = $upperLeftY + 5;


		echo "\e[{$upperRightX};{$upperRightY}H";
		$count= 0;
		while ($h >= $count) {
			echo "  ";
			$count++;
			$x = $upperRightX + $count;
			echo "\e[{$x};{$upperRightY}H";
		}




		echo "\e[{$messageStartX};{$messageStartY}H";
		echo "\e[32;40m";
		echo $msg;


	}


	public function drawMovingDude($r,$c,$t) {

		echo "\e[?25l";				//hides cursor
		echo "\e[40m";				//foreground black
		$n = $c + $t;
		$r -= 1;

		echo "\e[{$r};{$n}H";		//move cursor
		echo $t;
		$or = $r;
		$on = $n;


		$count = 0;
		$remains = $t;
		while ($count < $t) {
			echo "\e[{$r};{$c}H";
			echo '👻';
			echo "\e[{$or};{$on}H";
			$remains--;
			echo $remains;
			if ($remains == 10) {echo "\e[2D"; echo "  "; $on+=1;}
			$count++;
			$c += 1;
			sleep(1);

		}
		echo "\e[?25h";		//show cursor
	}



}

