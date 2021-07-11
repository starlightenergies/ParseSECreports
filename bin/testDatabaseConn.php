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
 * @filename:       TestDatabaseConn.php
 * @version:        1.0
 * @lastUpdate:    	2021-07-09
 * @author:         James Danforth <james@reemotex.com>
 * @pattern:		App-Monitor
 * @since:          2021-06-24
 * @controller:		Cron
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:
 */

/* usage
	- export these environment variables from your .bashrc file
	- then use them here
	- setup in cron with your email to notify you if connection fails
	- since many other programs in cron rely on same database connection
*/


//transfer needed setup info from private .bashrc file
$d = trim(shell_exec('echo ${DSN}'));
$u = trim(shell_exec('echo ${USER}'));
$p = trim(shell_exec('echo ${PASS}'));
$T = trim(shell_exec('echo ${TERM}'));
define('TERM', $T);

//get database handle
$db = new JDdatabase($d, $u, $p);
if(is_array($db)) {
	foreach ($db as $msg) {
		echo $msg . "\n";
	}
	exit(0);
}

$db->createDBHandle();

//Nette Resultset object returned
$vals = $db->showTables();
$count = 0;
$header = <<<END
\n
ID  	Name					       Symbol 		CEO
-------------------------------------------------------------------\n
END
;
echo $header;
foreach($vals as $row){
	$name = substr($row->name, 	0, 40);
	echo $row->id . "\t" . str_pad($name, 40) . "\t" . $row->symbol . "\t" . $row->ceo . "\n";
	$count++;
	if($count % 20 == 0){
		echo $header;
	}
}

exit(0);


