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
 * @filename:       Downloader.php
 * @version:        1.0
 * @lastUpdate:    	2021-07-06
 * @author:         James Danforth <james@reemotex.com>
 * @pattern:        App for testing Downloader
 * @since:          2021-07-04
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:      GuzzleHttp
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:        https://www.sec.gov/os/accessing-edgar-data
 */

/* usage:
	- below
*/

//create client
$dl = new Downloader();
$client = $dl->guzzleClient();

//set name of file to create with downloaded info
$file = "../data2/companyfacts.zip";

//set target to download (or provide URL string)
$target = $dl->bulk_data_url;

//set location to store data (or send location)
$dl->setGuzzleSink($file);

//setup resource to write to (..data2/companydata.zip, etc)
$dl->setGuzzleResource($file);

//setup guzzle stream (uses resource created above)
$dl->setGuzzleStream();

//set appropriate  media header (aka MIME type) for file type
// application/zip, text/xml, text/plain etc
$dl->setAcceptHeader('application/zip');

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
chdir("../data2");
shell_exec('rm -f *.json');
shell_exec('rm -f *.xml');
shell_exec('unzip companyfacts.zip');
shell_exec('rm -f companyfacts.zip');
