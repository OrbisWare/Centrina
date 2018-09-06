<?php
/**
 * Centrina PHP Framework
 *
 * An open-source lightweight development framework for PHP 5.6.x or newer
 *
 * @package		Centrina
 * @author John "Wishbone" Soica
 * @copyright Copyright (c) 2015 - 2018, OrbisWare, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/OrbisWare/Centrina
 * @since     Version 1.0
 *
 * @category  Middleware
 */
namespace Centrina\System\Middleware;

class GateKeeper{
	public static function loggedIn()
	{
		if( isset($_SESSION["user_id"]) || isset($_SESSION["string"]) )
		{

		}
	}
}
?>
