<?php
namespace JDApp;
include "../vendor/autoload.php";


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
 * @purpose:        XBRL Report Processing Application
 * @filename:       UpdateCIKdata.php
 * @version:        1.0
 * @lastUpdate:    	2021-07-01
 * @author:         James Danforth <james@reemotex.com>
 * @pattern:		App
 * @since:          2021-06-24
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:		pain in the butt :-)
 */

/* usage
	merges CIK data with Ticker symbols. surprising that
	SEC reports do not provide both in the same file.
	Having both in the same company record is an absolute must.
	Needs to run automated AFTER "update-Ticker-to-CIK-list.php" runs
	Still, until completely solved, must check CIK field to see if its
	still using default = zero. Keeping the company table up to date is
	definitely a challenge to full automation at the moment.
	this is partially mitigated by ignoring new company data. This is fine
	as new IPO's are really green and its more valuable just to watch them
	for a year or two while the market figures them out. if they are great,
	they will be great for decades.
*/

//boost memory allocated by php
$option = 'memory_limit';
$value = '4096M';
$oldMem = ini_set($option, $value);


$cikData = [];
//get cik list, this is updated by the update-Ticker-to-CIK-list.php file from time to time
$data = file("../includes/cik-to-ticker.txt");
$temp = [];
foreach($data as $key => $val) {

	echo "VAL: " . $val . "\n";
	if (preg_match("/^([a-z]+)\s+([0-9]+)$/", $val,$matches)) {
		echo "Match 1: " . $matches[1] . "\n";
		echo "Match 2: " . $matches[2] . "\n";
		$string = trim($matches[1]) . ":" . trim($matches[2]);
		array_push($cikData, $string);
	}

}

//reset memory allocated by php
$value = $oldMem;
ini_set($option, $value);

//export these environment variables from your .bashrc file
//then use them here

$d = trim(shell_exec('echo ${DSN}'));
$u = trim(shell_exec('echo ${USER}'));
$p = trim(shell_exec('echo ${PASS}'));
$T = trim(shell_exec('echo ${TERM}'));
define('TERM', $T);


//get database handle or report errors
$db = new JDdatabase($d, $u, $p);
if(is_array($db)) {
	foreach ($db as $msg) {
		echo $msg . "\n";
	}
	exit(0);
}
$db->createDBHandle();

//nette Resultset returned
$vals = $db->showTables();
$fh = fopen("../includes/company-ticker-final.txt",'w');
$count = 0;
$header = <<<END
\n
ID  	Name					        Symbol    CIK
--------------------------------------------------------------------------\n
END
;
echo $header;
foreach($vals as $row) {
	foreach ($cikData as $string) {
		list($ticker,$cik) = explode(':', $string);
		if ($ticker == strtolower($row->symbol)) {
			if($count % 18 == 0) { echo $header; }
			$cik = intval($cik);
			if(!is_int($cik)) {
				echo $cik . " not a number\n";
			}
			$mysymbol = $row->symbol;
			echo $row->id . "\t" . str_pad($row->name, 40) . "\t" . $mysymbol . "\t" . $cik . "\n";
			$newstring = $row->id . "," . $row->name . "," . $mysymbol . "," . $cik . "\n";
			fwrite($fh, $newstring);

			//the whole purpose of this app is to do this.
			//having CIK and SYMBOL together really important for managing SEC data
			//what remains to be done is handle CIK's for incoming IPO's
			$result = $db->storeCikValue($cik, $mysymbol);
			//echo "Dump: " . $result->dump() . "\n";
			$count++;
		}
	}
}

fclose($fh);
exit(0);
