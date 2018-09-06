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
 * @category  Core
 */
namespace Centrina\System\Core;

class Meta {
  //Someday I'll get around to coding a proper config system.
	public static function loadConfig($file)
	{
		$file = CT_APPPATH."/".$file;
		if(is_readable($file) === TRUE)
		{
			return (array) call_user_func(function() use($file) {
				return include_once($file);
			});
		}else{
			trigger_error("Core: Unable to include config file: ".$file, E_USER_NOTICE);
		}
	}

	//I'll get around to coding a proper language system.
	/*public static function _lang($key)
	{
		$file = CT_APPPATH."/langs/".$file;
		$lang = array();
		if(is_readable($file) === TRUE)
		{
			return (array) call_user_func(function() use($file) {
				$lang include_once($file);
			});
		}else{
			trigger_error("Core: Unable to include langauge file: ".$file, E_USER_NOTICE);
		}
	}*/
}
?>
