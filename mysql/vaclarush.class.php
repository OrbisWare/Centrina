<?php
/**
 * Centrina PHP Framework
 *
 * An open-source lightweight development framework for PHP 5.6.x or newer
 *
 * @package		Centrina
 * @author John "Wishbone" Soica
 * @copyright	Copyright (c) 2015 - 2017, Bad Wolf Systems Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/OrbisWare/Centrina
 * @since     Version 1.0
 *
 * @category  Core
 */

namespace Centrina\System\MySQL;
use Centrina\System\Core\CT_Core as CT_Core;
use \Exception;

class Vaclarush {
	private static $_sqlMe;
	public $sqlDB = array();

	public function connectDatabase($instance, $host, $user, $pass, $db, $port = null, $socket = null)
	{
		try {
			//DB connections aren't meant to be persistant in PHP so we're just going to test the connection.
			new SQLObject($host, $user, $pass, $db, $port, $socket);
			$this->sqlDB[$instance]["host"] = $host;
			$this->sqlDB[$instance]["user"] = $user;
			$this->sqlDB[$instance]["pass"] = $pass;
			$this->sqlDB[$instance]["db"] = $db;
			$this->sqlDB[$instance]["port"] = $port;
			$this->sqlDB[$instance]["socket"] = $socket;
		} catch (Exception $e) {
			echo "\n<b>Error - ".$e->getFile().":".$e->getLine()."</b> ".$e->getMessage()."\n";
		}
	}

	protected function quickConnect()
	{
		$c = CT_Core::loadConfig("config.php");
		//var_dump($c);

		try {
			$sql = new SQLObject($c["mysql_host"], $c["mysql_username"], $c["mysql_password"], $c["mysql_database"]);
			return $sql;
		}catch (Exception $e){
			echo "\n<b>Error - ".$e->getFile().":".$e->getLine()."</b> ".$e->getMessage()."\n";
			return;
		}
	}

	public static function getDatabase($instance = 0)
	{
		if( is_null(self::$_sqlMe) )
		{
			$obj = new Vaclarush();
			return $obj->quickConnect();
		}

		return self::$_sqlMe;
	}
}
?>
