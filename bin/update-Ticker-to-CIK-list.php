<?php
namespace JDApp;
include '../vendor/autoload.php';

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
 * @purpose:        XBRL Report Processing Application
 * @filename:       update-Ticker-to-CIK-list.php
 * @version:        1.0
 * @lastUpdate:     2021-07-06
 * @author:         James Danforth <james@reemotex.com>
 * @pattern:        App for keeping Tickers and CIK's up to date
 * @since:          2021-07-06
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:      GuzzleHttp
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:		https://www.sec.gov/include/ticker.txt
 */

/* usage:
	- The problem is tickers and CIK's are a must have in the company table of the database
	- but as new companies enter the market, their JSON reports do not have both keys.
	- so a program like this must keep the "cik-to-ticker.txt" file up-to-date with
	- an occasional call to the most recent file on SEC website (well at least we think its
	- the most recent). Then, the updateCIKdata.php can be run to update the database with new CIK's
*/

//backup the old cik list
$cwd = getcwd();
chdir('../includes');
$file1 = 'cik-to-ticker.txt';
$file2 = 'cik-to-ticker.txt.bak';
shell_exec('cp -f ' . $file1 . ' ' . $file2 );
	if(file_exists($file2)) {
		echo 'backup success\n';
	}


//create client
$dl = new Downloader();
$client = $dl->guzzleClient();

//set name of file to create with downloaded info
$file = "../includes/cik-to-ticker.txt";

//set target to download (or provide URL string)
$target = $dl->cik_list;

//set location to store data (or send location)
$dl->setGuzzleSink($file);

//setup resource to write to (..data2/companydata.zip, etc)
//this truncates the file, thats why we back it up first above
$dl->setGuzzleResource($file);

//setup guzzle stream (uses resource created above)
$dl->setGuzzleStream();

//set appropriate header for file type
// application/zip, text/xml, text/plain etc
$dl->setAcceptHeader('text/plain');

//download the file with get method
$response = $dl->downloadSEFile($client, $target);

//display common response codes and headers
$dl->getCommonResponseCodes($response);

//check download file status
$dl->checkDownloadFileStatus($file);

//list headers separately(optional)
$dl->listHeadersOnly($response);

//close file resource
$dl->closeFileHandle();

//post download work
chdir($cwd);