<?php
namespace JDApp;
use JDApp\ProcessFiles as Files;
include "vendor/autoload.php";
require_once "includes/programDefines.inc";

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
 * @filename:    	ReportProcessor.php
 * @version:    	1.0
 * @lastUpdate:  	2021-07-09
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

/* usage
	- processes each new JSON company facts file and stores into financial database
	- see documentation and flowcharts in docs folder and flowcharts folder TODO
	- the data is analyzed and presented in www.vaultbear.com as time permits...
*/


//boost memory allocated by php
$option = 'memory_limit';
$value = '4096M';
$oldMem = ini_set($option, $value);

//export these environment variables from your .bashrc file
//then use them here

$d = trim(shell_exec('echo ${DSN}'));
$u = trim(shell_exec('echo ${USER}'));
$p = trim(shell_exec('echo ${PASS}'));
$T = trim(shell_exec('echo ${TERM}'));
define('TERM', $T);

//get database handle
$db = new JDdatabase($d,$u,$p);
$db->createDBHandle();

//resultset returned
$vals = $db->showTables(10);
$count = 0;
$header = <<<END
\n
ID  	Name					       		    Symbol 
--------------------------------------------------------\n
END
;
echo $header;
foreach($vals as $row){

	echo $row->id . "\t" . str_pad($row->name,40) . "\t" . $row->symbol . "\n";
	$count++;
	if($count % 20 == 0){
		echo $header;
	}
}

//resultset returned (id, cik, symbol where active and cik != 0
//at the end of a file processing this record set is sent
//to the Record just processed to aid database entries for its
//data objects and entry objects

//now array returned, result set cant be rewound...
$active_stocks = $db->selectActiveStocksList();

//only process new files. this will gradually update data directory
//data directory is the foundation/basis for the database

$filestore = [];
$dir = __DIR__ . '/data2';
echo $dir;

if ($dirHandle = opendir($dir)) {
	echo "\ngot filehandle\n";
}


//read directory to get file names
//process them one at a time through loop
while ($file = readdir($dirHandle)) {


		if ($file == '.' || $file == '..'||preg_match("/.save$/", $file)
			||preg_match("/.bak$/", $file)) {
			continue;
		}

		$file = $dir . "/" . $file;
		if(filesize($file) < 100 ) {
			//ignore essentially empty files.
			continue;
		}

	$fileProc = new Files($file);
		//get objects needed after each file is processed
		$Record = $fileProc->Record;
		$State = $fileProc->State;
		$Action = $fileProc->Activity;



		$SPL_file_object = $fileProc->createFileHandle('r');
		while (!$SPL_file_object->eof()) {
			$char = $SPL_file_object->fgetc();										//get character from file object
			$type = $fileProc->examineCharacter($char);						//process character with processFile object
		}

		//view data summary when file processing complete
		$Action->collectedDataSummary($Record,$State);

		//review file stat
		echo "Overall: " . $Record->company_name . "\t" . $Record->cik . "\t" . count($Record->dataStore) . "\n";

		// initiate cascade of database entries by passing $db object and
		// Resultset object holding qualified stocks to update from the Company table.
		// the Record that just finished processing above is called below and
		// will store its data and call each data object in its datastore to
		// store its data, then each data object will call each entry in its
		// entry store to store their data. And then on to the next record...TODO

		$insertID = $Record->storeRecordData($db, $active_stocks);
		if($insertID == 0) {

			//add to company table
			if(!empty($Record->company_name)) {
				$status = 'y';
				$insertID = $db->addNewCompany($Record->company_name, $status, $Record->cik);
				if($insertID > 0) {
					//try to process report again.
					//rebuild array...
					$active_stocks = $db->selectActiveStocksList();
					$insertID = $Record->storeRecordData($db, $active_stocks);
					echo "2nd Try insertID: " . $insertID . " and storageFlag: " . $Record->storedInDb . "\n";
					sleep(5);

				}
			} else {
				echo "Company CIK not found, discontinue work, fail logged\n";
				//log entry into file
				$handle = fopen(__DIR__ . "/logs/report-processor.log", 'a');
				$date = trim(shell_exec('date +%Y-%m-%d-%H-%M-%S'));
				$record = $date . ", " . $Record->cik . ", " . $Record->company_name . ", " . "DB financial record insert failed\n";
				fwrite($handle, $record);
				fclose($handle);
				sleep(5);

			}
		}

		//more to come here TODO


} //end of main loop

closedir($dirHandle);

//cleanup files processed here TODO


//reset memory allocated by php
$value = $oldMem;
ini_set($option, $value);


/*  notes below

foreach ($filestore as $file) {
	echo $file->getFilename() . " name from store\n";
	echo $file->getMaxLineLen() . " maximum line length\n";
	echo $file->key() . " current line number\n";
	echo $file->ftell() . " current file position\n";
	echo $file->fgetc() . " current character\n";
	$info = $file->fstat();									//https://www.php.net/manual/en/splfileobject.fstat.php
	$file->fseek(-2, SEEK_END);
	echo $file->fgetc() . " last character\n";
	$file->rewind();
	echo $file->fgetc() . " first character\n";
	$file->fseek(0, SEEK_END);
	echo "At end of file: " . $file->eof() . "\n";
	$file->rewind();
	$file->fseek(0,SEEK_SET);
	echo "At end of file: " . $file->eof() . "\n";
	echo $file->fgetc() . " first character\n";
	$file->fseek(1,SEEK_CUR);
	echo $file->fgetc() . " next character\n";
}

*/

exit(0);
