<?php
/**********************************************************************************
*     NewsCenter
*     /System/Class/Mysql.php
*     Version: $Id: Mysql.php,v 1.1 2004/10/08 21:25:29 jcrawford Exp $
*     Copyright (c) 2004, The NewsCenter Development Team

*     Permission is hereby granted, free of charge, to any person obtaining
*     a copy of this software and associated documentation files (the
*     "Software"), to deal in the Software without restriction, including
*     without limitation the rights to use, copy, modify, merge, publish,
*     distribute, sublicense, and/or sell copies of the Software, and to
*     permit persons to whom the Software is furnished to do so, subject to
*     the following conditions:

*     The above copyright notice and this permission notice shall be
*     included in all copies or substantial portions of the Software.

*     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
*     EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
*     MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
*     NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
*     BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
*     ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
*     CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*     SOFTWARE.
*********************************************************************************/

class Mysql extends Database {

	public function __construct($hostname=null, $database=null, $username=null, $password=null) {
		$this->hostname = $hostname;
		$this->database = $database;
		if(is_null($username)) $this->username = 'root';
		else $this->username = $username;
		$this->password = $password;
	}

	public function __destruct() {
		$this->close();
	}

	public function __set($name, $value) {
		if (isset($name) && isset($value)) {
			parent::__set($name, $value);
		}
	}

	public function __get($name) {
		if (isset($name)) {
			return parent::__get($name);
		}
	}

	public function connected() {
		if (is_resource($this->connection)) {
			return true;
		} else {
			return false;
		}
	}

	public function affectedRows() {
		return @mysql_affected_rows($this->connection);
	}

	public function open() {
		if (is_null($this->database)) {
			throw new DatabaseEx('Database Not Selected!');
		} elseif (is_null($this->hostname)) {
			throw new DatabaseEx('MySQL Hostname Not Set!');
		}
		$this->connection = @mysql_connect($this->hostname, $this->username, $this->password);

		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		}

		if (!$this->selectDb($this->database, $this->connection)) {
			throw new DatabaseEx('could not select database \''.$this->database.'\' on server \''.$this->hostname.'\'!');
		}
	}

	public function selectDb($database) {
		if(!@mysql_select_db($database)) {
			throw new DatabaseEx('Unable to select the database: '.$database);
		} else {
			$this->database = $database;
			return TRUE;
		}
	}
	
	public function close() {
		@mysql_close($this->connection);
		$this->connection = null;
	}

	public function query($sql) {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		}

		$result = @mysql_query($sql,$this->connection);
		if(!is_resource($result)) {
			throw new DatabaseEx('Invalid SQL query!');
		} elseif ($this->numRows($result) == 0) {
			throw new DatabaseEx('Empty Result Set Returned!');
		}
		return $result;
	}

	public function fetchArray($result) {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		}
		if(!is_resource($result)) {
			throw new DatabaseEx('Invalid Resource For fetchArray()');
		}
		$data = @mysql_fetch_array($result);


		return $data;
	}

	public function fetchAssoc($result) {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		}

		$data = @mysql_fetch_assoc($result);
		if (!is_array($data)) {
			throw new DatabaseEx('Invalid resource used in fetchAssoc!');
		}
		return $data;
	}

	public function numRows($result) {
		return @mysql_num_rows($result);
	}

	public function numFields($result) {
		return @mysql_num_fields($result);
	}

	function lastInsertId() {
		return @mysql_insert_id();
	}
	function realEscape($string) {
		return @mysql_real_escape_string($string);
	}
}
?>