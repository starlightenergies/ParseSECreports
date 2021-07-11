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
 * @filename:       updateListedStatusFiles.php
 * @version:        1.0
 * @lastUpdate:    	2021-07-08
 * @author:         James Danforth <james@reemotex.com>
 * @pattern:		Download App
 * @since:          2021-07-08
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:		JDApp\Downloader
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:		CSV files from Alphavantage.co with free API key
 */

/* usage
	-- runs in cron

*/
//backup old files
chdir("../includes");
shell_exec('cp -f listing_status.csv listing_status.csv.bak');
shell_exec('cp -f listing_status-2.csv listing_status-2.csv.bak');
chdir("../bin");

//build https requests
$apikey = trim(shell_exec('echo ${ALPHA_API_KEY}'));
$activeListReq = "http://www.alphavantage.co/query?function=LISTING_STATUS&apikey=" . $apikey;
$date = trim(shell_exec("date +%Y-%m-%d"));
$deListedReq = "https://www.alphavantage.co/query?function=LISTING_STATUS&date=" .$date ."&state=delisted&apikey=" . $apikey;


//active call
$file1 = "../includes/listing_status.csv";
$data1 = file_get_contents($activeListReq);
file_put_contents($file1, $data1);

sleep(20);

//inactive call
$file2 = "../includes/listing_status-2.csv";
$data2 = file_get_contents($deListedReq);
file_put_contents($file2, $data2);


