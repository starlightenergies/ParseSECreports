<?php
namespace JDApp;
use Exception;
use Nette\Database\Connection;
use Nette\Database\ConnectionException;
use Nette\Database\DriverException;
use Nette\Database\ResultSet;
use Nette\Database\DriverException as DException;


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
 * @filename:	JDdatabase.php
 * @version:  	1.0
 * @lastUpdate:  2021-07-09
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
	const ERRORMSG = "Error on select: ";

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
			foreach($vals as $msg) {
				echo $msg . "\n";
			}
			$this->database='see errors';
			return $vals;
		}

		empty($vals) ? $this->errors = 'none': $this->errors = $vals;
		return $dbh;
	}

	public function updateStatus($status,$symbol): ResultSet {
		//add active status
		$dbh = $this->database;
		if (is_string($dbh)) { echo $dbh . "\n"; }

		$query = "UPDATE company SET activestatus=" . "'" . $status . "'" .  " WHERE symbol=" . "'" . $symbol . "'" ;
		try {
			$results = $dbh->query($query);
		}  catch (Exception $e) {
			$message = $e->getMessage();
			echo self::ERRORMSG . $message . "\n";
		}
		return $results;
	}

	public function addCompany($symbol, $name, $status): ResultSet {
		//add symbol and name and active status
		$dbh = $this->database;
		if (is_string($dbh)) { echo $dbh . "\n"; }

		$query = "INSERT INTO company (symbol,name,activestatus) VALUES (" .
			"'" . $symbol . "'" . "," . "'" .$name . "'" . "," . "'" . $status . "'" . ")";

		try {
			$results = $dbh->query($query);
			echo "Insert ID: " . $dbh->getInsertId() . "\n";
			//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount
  		}  catch (Exception $e) {
			$message = $e->getMessage();
			echo self::ERRORMSG . $message . "\n";
		}

		return $results;
	}

	public function addNewCompany($name, $status, $cik): int {
		//add symbol and name and active status
		$dbh = $this->database;
		if (is_string($dbh)) { echo $dbh . "\n"; }

		//check if already in database
		$val = $this->checkCompanyExistence($cik);

		//if not, add
		if($val == 0) {
			$query = "INSERT INTO company (name,activestatus,cik) VALUES (" .
				"'" . $name . "'" . "," . "'" . $status . "'" . "," . "'" . $cik . "'" . ")";
			try {
				$dbh->query($query);
				echo "Insert ID in DB Class: " . $dbh->getInsertId() . "\n";
				$val = $dbh->getInsertId();
				//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount
			} catch (Exception $e) {
				$message = $e->getMessage();
				echo self::ERRORMSG . $message . "\n";
				$val = 0;
			}
		}

		return $val;
	}


	public function checkCompanyExistence($cik): int {

		$dbh = $this->database;
		if(is_string($dbh)) { echo $dbh . "\n"; }

		$query = "SELECT name from company where cik=" . "'" . $cik . "'";
		try {
			//resultset obj
			$results = $dbh->query($query);
			$results = $results->getRowCount();
			//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount

		} catch (Exception $e) {

			$message = $e->getMessage();
			echo self::ERRORMSG . $message . "\n";

		}
		return $results;
	}



	public function checkTickerExistence($sym): ResultSet {

		$dbh = $this->database;
		if(is_string($dbh)) {
			echo $dbh . "\n";
		}

		$query = "SELECT name from company where symbol=" . "'" . $sym . "'";
		try {
			//resultset obj
			$results = $dbh->query($query);
			//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount

		} catch (Exception $e) {

			$message = $e->getMessage();
			echo self::ERRORMSG . $message . "\n";

		}
		return $results;
	}

	public function showTables(int $limit): ResultSet {

		$dbh = $this->database;
		if(is_string($dbh)) {
			echo $dbh . "\n";
		}

		$query = "SELECT id,name, symbol,ceo from company ORDER BY symbol limit $limit";
		try {
			//resultset obj
			$results = $dbh->query($query);

			//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount

		} catch (Exception $e) {

			$message = $e->getMessage();
			echo self::ERRORMSG . $message . "\n";

		}
		return $results;
	}


	public function storeCikValue($cik,$ticker): ResultSet {
		//puts CIK into company table
		$dbh = $this->database;

		try {
			//resultset obj
			$results = $dbh->query('UPDATE company set', [
				'cik'	=> $cik,
			], 'where symbol = ?', $ticker);

			$results->getRowCount();
			//getQueryString() dump() getRowCount getColumnCount getParameters getInsertId()

		} catch (Exception $e) {

			$message = $e->getMessage();
			echo self::ERRORMSG . $message . "\n";

		}
		return $results;
	}


	//database entries from Report Processor Start Here.

		public function selectActiveStocksList(): ?array {

			$dbh = $this->database;
			$active = 'y';
			$query = "SELECT id, cik, symbol from company where cik!=0 and activestatus = " . "'" . $active . "'";
			try {
				//resultset obj -
				//$results = $dbh->query($query);
				//array returned
				$results = $dbh->fetchAll($query);
				//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount
			} catch(DException $e) {
				$message = $e->getMessage();
				echo $message . "\n";

			}
			return $results;

		}

		public function enterRecordInfo(int $co_id, int $rec_id, int $store_count): int {

			//called by FinancialRecord Class to store its data
			$dbh = $this->database;

			$query = "INSERT INTO financialrecords (company_id, datastore_count, record_id) VALUES (" .
				"'" . $co_id . "'" . "," . "'" .$store_count . "'" . "," . "'" . $rec_id . "')";

			try {
				$dbh->query($query);
				$val = $dbh->getInsertId();
				echo "Insert ID in DB Class: " . $dbh->getInsertId() . "\n";
				sleep(5);
				//getQueryString() dump() getRowCount getColumnCount getParameters getRowCount
			}  catch (Exception $e) {
				$message = $e->getMessage();
				echo self::ERRORMSG . $message . "\n";
				sleep(5);
				$val = 0;
			}
			return $val;

		}

} //end of class