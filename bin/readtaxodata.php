<?php
namespace JDApp;
//this file is just used for testing. Its incorporated into a method
//in taxonomyTerms class that loads and updates taxonomy terms from a file
//in the includes dir


$file = __DIR__ . '/includes/taxonomy_terms.txt';
$fileProc = new \SplFileObject($file, 'r');

$header = [];
$measure = [];
$data = [];
$datatype = [];
$flag = 0;
$comment = '^#.*';
$empty = '^$';

while (!$fileProc->eof()) {
	//skip comment and empty lines
	$line = $fileProc->fgets();
	if (preg_match("/$comment/", $line) || preg_match("/$empty/", $line)) {
		continue;
	}

	//set flag for now only using header and datatype. will expand this later TODO
	if (preg_match("/^\[/", $line)) {
		switch (trim($line)) {
			case '[header]':
				$flag = 1;
				break;
			case '[measurement]':
				$flag = 4;
				break;
			case '[data]':
				$flag = 4;
				break;
			case '[datatype]':
				$flag = 4;
				break;
			default:
		}

	} else {
		//store line
			switch ($flag) {
			case '1':
				$info = trim($line);
				array_push($header,$info);
				break;
			case '2':
				$info = trim($line);
				array_push($measure,$info);
				break;
			case '3':
				$info = trim($line);
				array_push($data,$info);
				break;
			case '4':
				$info = trim($line);
				array_push($datatype,$info);
				break;
			default:
		}
	}
}

//package arrays for return only two used at moment
$package = [];
array_push($package, $header);
array_push($package, $datatype);

foreach($package as $val) {
	foreach ($val as $myval) {
		echo "myvals: " . $myval . "\n";
		sleep(1);
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


