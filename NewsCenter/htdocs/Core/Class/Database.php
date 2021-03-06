<?php
/**********************************************************************************
*     NewsCenter
*     /System/Class/Database.php
*     Version: $Id: Database.php,v 1.1 2004/10/08 21:25:29 jcrawford Exp $
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
abstract class Database implements IConnector {
	// an array of properties used by __get and __set
	private $props;
	
	// the actual connection resource
	public $connection;
	
	// the hostname for the database server
	public $hostname;
	
	// the name of the database to use
	public $database;
	
	// the username to use to access the database
	protected $username;
	
	// the password to use to access the database
	protected $password;
	
	protected function __set($name, $value) {
		if (isset($this->props[$name])) {
			$this->props[$name] = $value;
		}
	}
	
	protected function __get($name) {
		if (isset($this->props[$name])) {
			return $this->props[$name];
		} else {
			return nulll;	
		}
	}
	
	public function open() {
	}
	
	public function close() {
	}
	
	public function connected() {
	}
	
	abstract public function query($sql);
	abstract public function fetchArray($result);
	//abstract public function fetchAll($result);
	//abstract public function numRows($result);
}
?>