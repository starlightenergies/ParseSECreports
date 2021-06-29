<?php
namespace JDApp;
use JDApp\ProcessFiles as Files;
include "vendor/autoload.php";
require_once "includes/programDefines.inc";


/**
 * @purpose:    	Report Processing Application
 * @filename:    	ReportProcessor.php
 * @version:    	1.0
 * @lastUpdate:  	2021-06-24
 * @author:        	James Danforth <james@workinout.com>
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


//get stock symbols from from database
//build curl or http request for all file data
//store datafiles in directory
//read directory to get file names
//process them one at a time through loop

$filestore = [];
$dir = __DIR__ . '/data';
echo $dir;

if ($dirHandle = opendir($dir)) {
	echo "\ngot filehandle\n";
}

while ($file = readdir($dirHandle)) {

		if ($file == '.' || $file == '..') {
			continue;
		}

		$file = $dir . "/" . $file;
		$fileProc = new Files($file);
		array_push($filestore,$fileProc);

}
closedir($dirHandle);

//filestore has filename and ProcessFile Object
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
