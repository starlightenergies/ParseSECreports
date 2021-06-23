<?php
namespace JDApp;
use Exception;
use Nette\Database\Connection;
use Nette\Database\ConnectionException;
use Nette\Database\ResultSet;

/**
 * @purpose:	Stores financial data into mysql database
 * @filename:	Database.php
 * @version:  	1.0
 * @lastUpdate:  2021-04-04
 * @author:    	James Danforth <james@workinout.com>
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