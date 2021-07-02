<?php
namespace JDApp;
use Exception;
use Nette\Database\Connection;
use Nette\Database\ConnectionException;
use Nette\Database\ResultSet;


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
 * @purpose:	XBRL Report Processing Application
 * @filename:	Database.php
 * @version:  	1.0
 * @lastUpdate:  2021-04-04
 * @author:    	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:   	2021-04-04
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:
 */

class JDdatabase {

	public $database;
	public $dsn;
	public $user;
	public $password;
	public $errors;

	public function __construct($dsn,$user,$password) {
		$this->dsn = $dsn;
		$this->user = $user;
		$this->password = $password;
	}

	public function createDBHandle() {
		$vals = [];
		$ds = $this->dsn;
		$usr = $this->user;
		$passwd = $this->password;

		try {
			$dbh = new Connection($ds, $usr, $passwd);
			$this->database = $dbh;
		}
		catch(ConnectionException $e) {
			$vals[] = $e->getMessage();
			$vals[] = $e->getDriverCode();
			$vals[] = $e->getParameters();
			$vals[] = $e->getQueryString();
			$vals[] = $e->getSqlState();
			$this->database='screwed';
			return $vals;
		}

		empty($vals) ? $this->errors = 'none': $this->errors = $vals;
		return $dbh;
	}

	public function showTables(): ResultSet {

		$dbh = $this->database;
		$query = "SELECT * from company";
		try {
			//resultset obj
			$results = $dbh->query($query);

			//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount

		} catch (Exception $e) {

			$message = $e->getMessage();
			echo "Error on select: " . $message . "\n";

		}
		return $results;
	}
}