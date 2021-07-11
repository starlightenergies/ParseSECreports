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
 * @filename:       updateListingStatus.php
 * @version:        1.0
 * @lastUpdate:    	2021-07-08
 * @author:         James Danforth <james@reemotex.com>
 * @pattern:
 * @since:          2021-06-24
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:		updates company table in stockengine database
 */

/* usage

*/

//export these environment variables from your .bashrc file
//then use them here

$d = trim(shell_exec('echo ${DSN}'));
$u = trim(shell_exec('echo ${USER}'));
$p = trim(shell_exec('echo ${PASS}'));
$T = trim(shell_exec('echo ${TERM}'));
define('TERM', $T);

//get database handle
$db = new JDdatabase($d, $u, $p);
$db->createDBHandle();

$count1 = $count2 = $count3 = 0;
$status = 'y';


if (($handle = fopen("../includes/listing_status.csv", "r")) !== FALSE) {
	//symbol,name,exchange,assetType,ipoDate,delistingDate,status
	while (($listed_data = fgetcsv($handle, 502, ",")) !== FALSE) {

		if ($listed_data[3] == 'ETF') { continue; }
		echo "symbol: " . $listed_data[0] . "\n";
		$symbol = trim(substr($listed_data[0],0,8));							//make sure no longer than 8 char
		echo "name: " . $listed_data[1] . "\n";
		$name = $listed_data[1];
		echo "exchange: " . $listed_data[2] . "\n";
		echo "type: " . $listed_data[3] . "\n";
		echo "ipodate: " . $listed_data[4] . "\n";
		echo "delist date: " . $listed_data[5] . "\n";
		echo "status: " . $listed_data[6] . "\n";

		//check existence in database
		$resultSet = $db->checkTickerExistence($symbol);
		if ($resultSet->getRowCount() == 0) {
			echo $listed_data[0] . " not found\n\n";
			$count1++;
			//since this is the active list, then need to add entry
			//add symbol and name and active status
			$results = $db->addCompany($symbol, $name, $status);
			if($results->getRowCount() != 0) {
				echo "successfully added company\n\n";
				$count3++;
			}

		} else {
			foreach ($resultSet as $row) {
				echo "Found " . $symbol . " for " . $row->name . "\n\n";
				//set active status = y
				$res = $db->updateStatus($status,$symbol);
				echo "Setting Active Status, Row count: " . $res->getRowCount() . "\n";
				$count2++;
				}
		}
	}
}

fclose($handle);
echo "Found " . $count2 . " symbols in DB\n";
echo "Did not find " . $count1 . " symbols in DB\n";
echo "Added " . $count3 . " symbols to DB";

//now look at de-listed file

$count1 = $count2 = $count3 = 0;
if (($handle = fopen("../includes/listing_status-2.csv", "r")) !== FALSE) {
	//symbol,name,exchange,assetType,ipoDate,delistingDate,status
	while (($delisted_data = fgetcsv($handle, 502, ",")) !== FALSE) {

		if ($delisted_data[3] == 'ETF') { continue; }
		echo "symbol: " . $delisted_data[0] . "\n";
		$symbol = trim(substr($delisted_data[0],0,8));							//make sure no longer than 8 char
		echo "name: " . $delisted_data[1] . "\n";
		$name = $delisted_data[1];
		echo "exchange: " . $delisted_data[2] . "\n";
		echo "type: " . $delisted_data[3] . "\n";
		echo "ipodate: " . $delisted_data[4] . "\n";
		echo "delist date: " . $delisted_data[5] . "\n";
		echo "status: " . $delisted_data[6] . "\n";


		//check existence in database
		$resultSet = $db->checkTickerExistence($symbol);
		if ($resultSet->getRowCount() == 0) {
			echo $delisted_data[0] . " not found\n\n";
			$count1++;
			//since this is the de-active list, then no need proceed

		} else {
			$status = 'n';
			foreach ($resultSet as $row) {
				echo "Found " . $row->symbol . " for " . $name . "\n\n";
				//set active status = y
				$res = $db->updateStatus($status,$symbol);
				echo "Setting Active Status, Row count: " . $res->getRowCount() . "\n";
				$count2++;
				sleep(2);
			}
		}
	}
}


fclose($handle);
echo "Found " . $count2 . " delisted symbols in DB\n";
echo "Did not find " . $count1 . " symbols in DB\n";
echo "Added " . $count3 . " symbols to DB";
