<?php
/**********************************************************************************
*     NewsCenter
*     /System/Class/Sqlite.php
*     Version: $Id: Sqlite.php,v 1.1 2004/10/08 21:25:29 jcrawford Exp $
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

class DB extends Database {

	private $error;
	private $permission;

	private function __construct($dbName, $dbPerms) {
		$this->database = $dbName;
		$this->permission = $dbPerms;
	}

	public function Open() {
		if(is_null($this->database)) {
			die("Sqlite database not selected");
		}
		if (is_null($this->permission)) {
			die("Sqlite permissions not set");
		}
		if(is_resource($this->connection)) {
			throw new DatabaseEx('Already connected to a database server!');	
		}
		if(file_exists($this->database)) {
			$this->connection = sqlite_open($this->database, $this->permission, $this->error);
		} else {
			$this->connection = sqlite_open($this->database, $this->permission, $this->error);
		}

		if ($this->connection === false) {
			die($this->error);
		}
	}

	public function Close() {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		}
		sqlite_close($this->connection);
		$this->connection = null;
	}

	public function Query($sql) {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		}
		$result = sqlite_query($this->connection, $sql);
		if (!is_resource($result)) {
			throw new DatabaseEx('Invalid SQL Statement.');
		}
		return $result;
	}

	public function FetchArray($result) {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		} elseif(!is_resource($result)) {
			throw new DatabaseEx('Invalid Resource For fetchArray()');
		}
		$data = @sqlite_fetch_array($result);
		return $data;
	}

	public function FetchAll($result) {
		if ($this->connection === false) {
			throw new DatabaseEx('Not connected to a database server!');
		} elseif(!is_resource($result)) {
			throw new DatabaseEx('Invalid Resource For fetchAll()');
		}
		$data = @sqlite_fetch_all($result);
		return $data;
	}

	function table_exists($table) {

		return sqlite_fetch_single($table) > 0;
	}
}
?>