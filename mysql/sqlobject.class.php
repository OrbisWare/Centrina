<?php
/**
 * Centrina PHP Framework
 *
 * Orginally written by Kevin "Tigerkev" H for project Aura, refined for Centrina.
 * An open-source lightweight development framework for PHP 5.6.x or newer
 *
 * @package		Centrina
 * @author    John "Wishbone" Soica
 * @copyright	Copyright (c) 2015 - 2018, OrbisWare, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/OrbisWare/Centrina
 * @since     Version 1.0
 *
 * @category  Core
 */
namespace Centrina\System\MySQL;
use \Exception;
use \mysqli;

class SQLObject {
	protected $_createQueue = array();
	protected $connected;

	protected $_mysqli;

	public function __construct($host, $user, $pass, $db, $port = 3306, $socket = null)
	{
		if($this->connected === true)
		{
			throw new Exception("Already connected to a MySQL database!");
			return;
		}else{
			$this->connect($host, $user, $pass, $db, $port, $socket);
		}
	}

	private function connect($host, $user, $pass, $db, $port)
	{
		$this->_mysqli = new mysqli($host, $user, $pass, $db, $port);

		if($this->_mysqli->connect_errno)
		{
			$this->connected = false;
			throw new Exception("MySQLi Connection Error: " . $this->_mysqli->connect_errno . ': ' . $this->_mysqli->connect_error, $this->_mysqli->connect_errno);
			return;
		}

		$this->connected = true;
	}

	public function isConnected()
	{
		return $this->connected;
	}

	public function close()
	{
		if($this->connected)
		{
			$this->_mysqli->close();
			$this->connected = false;
		}
	}

	public function query($qry, $res = MYSQLI_STORE_RESULT)
	{
		$st = $this->_mysqli->query($qry, $res);
		if(false===$st)
		{
			trigger_error("query() failed: ".$this->_mysqli->error, E_USER_WARNING);
			debug_print_backtrace();
		}

		return new DBObject($st);
	}

 	public function prepare($qry)
	{
		$st = $this->_mysqli->prepare($qry);
		if(false===$st)
		{
			trigger_error("prepare() failed: ".$this->_mysqli->error, E_USER_WARNING);
			debug_print_backtrace();
		}

		return new DBObject($st);
	}

	public function affectedRows()
	{
		return $this->_mysqli->affected_rows;
	}

	public function addCreationQueue(array $arr)
	{
		$this->_createQueue = array_merge($this->_createQueue, $arr);
	}

	public function getCreationQueue()
	{
		return $this->_createQueue;
	}
}
?>
