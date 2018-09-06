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
 * @category  Library
 */

namespace Centrina\System\Libs;
use Centrina\System\Core\CT_Core as CT_Core;

class Vivian {
	public static function smartHash($str, $prefix = true)
	{
		$config = CT_Core::loadConfig("config.php");
		$hashes = CT_Core::loadConfig("hashes.php");

		$result;
		$vers = $config["hash_version"];
		$hash = $hashes[$vers];

		$result = hash($hash, $str);
		if($prefix === TRUE)
		{
			$result = $vers . "$" . hash($hash, $str);
		}

		return $result;
	}

	public static function hashCompare($known, $str)
	{
		$config = CT_Core::loadConfig("config.php");
		$hashes = CT_Core::loadConfig("hashes.php");

		$knownVers = strstr($known, "$", true);
		$strVers = strstr($str, "$", true);

		return array(
			hash_equals($knownVers, $strVers), //arg2 - if the hash versions match.
			hash_equals($known, $str) //arg1 - if the strings match.
		);
	}
}
?>