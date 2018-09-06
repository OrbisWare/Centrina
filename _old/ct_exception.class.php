<?php
/**
 * Centrina PHP Framework
 *
 * In honorem Dei Amy.
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
namespace Centrina\System\Core;
use \Exception;

class CT_Exception extends Exception {
	public function errorMessage()
	{
		$error = "\n<b>Error - ".$this->getFile().":".$this->getLine()."</b> ".$this->getMessage()."\n";
		$log = new Log(CT_Core::getDatabase());

		try {
			$log->writeLog(2, $error);
		}catch(Exception $e){
			echo "\n<b>Error - ".$e->getFile().":".$e->getLine()."</b> ".$e->getMessage()."\n";
			die();
		}

		return $error;
	}
}
?>