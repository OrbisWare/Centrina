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
namespace Centrina\System\Core;

class Hooks {
	private static $hooks = array();
	private static $hookEvents = array();

	public static function call($event, &...$args)
	{
		$hookEvents[] = $event;
		if(empty($args))
			return;

		foreach(self::$hooks[$event] as $func)
		{
			if(is_callable($func) === FALSE)
			{
				trigger_error("Core: Unable to call hook ".$event." with arguments ".implode(",", $args), E_USER_ERROR);
				return;
			}

			call_user_func_array($func, $args);
		}
	}

	public static function add($event, $id, $callback)
	{
		self::$hooks[$event][$id] = $callback;
	}

	public static function list()
	{
		return $hooks;
	}
}
?>