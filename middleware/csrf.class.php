<?php
/**
 * Centrina PHP Framework
 *
 * An open-source lightweight development framework for PHP 5.6.x or newer
 *
 * @package		Centrina
 * @author    John "Wishbone" Soica
 * @copyright	Copyright (c) 2015 - 2018, OrbisWare, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/OrbisWare/Centrina
 * @since     Version 1.0
 *
 * @category  Global Middleware
 */
use Centrina\System\Libs\Utils as Utils;

class CSRF {
	public static function createToken()
	{
		$token = sha1(Utils::generateSecureStr(16));
		$_SESSION["ct_token"] = $token;
		echo "<input type='hidden' name='ct_token' value='".$token."' />";
	}

	public static function checkToken()
	{
		self::deleteToken();
		return hash_equals($_SESSION["ct_token"], $_POST["ct_token"]);
	}

	public static function deleteToken()
	{
		unset($_SESSION["ct_token"]);
	}
}
?>
