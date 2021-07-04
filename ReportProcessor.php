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
 * @lastUpdate:  	2021-07-01
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


//get stock symbols from from database  //TODO
//build curl or http request for all file data -- may need SEC API //TODO
//store datafiles in directory	//TODO

//read directory to get file names -- below
//process them one at a time through loop -- below

//boost memory allocated by php
$option = 'memory_limit';
$value = '4096M';
$oldMem = ini_set($option, $value);


$filestore = [];
$dir = __DIR__ . '/data';
echo $dir;

if ($dirHandle = opendir($dir)) {
	echo "\ngot filehandle\n";
}

while ($file = readdir($dirHandle)) {

		if ($file == '.' || $file == '..'||preg_match("/.save$/", $file)
			||preg_match("/.bak$/", $file) ) {
			continue;
		}

		$file = $dir . "/" . $file;
		$fileProc = new Files($file);
		//get objects needed for after each file is processed
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

}
closedir($dirHandle);

echo "found " . count($filestore) . " files\n";
sleep(3);
/*
//filestore has filename and ProcessFile Object and
//each of these holds the complete financial record of the file processes, (intuit, tesla etc)
foreach ($filestore as $file_obj) {

	//create a file object from the filename with processFile object .. synced
	$SPL_file_object = $file_obj->createFileHandle('r');
	$filenameObjects[$file_obj->currentFile] = $SPL_file_object;

	foreach ($filenameObjects as $filename => $object) {

		while (!$object->eof()) {
			$char = $object->fgetc();										//get character from file object
			$type = $file_obj->examineCharacter($char);						//process character with processFile object
		}
	}
}
*/


//reset memory allocated by php
$value = $oldMem;
ini_set($option, $value);

/*
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
