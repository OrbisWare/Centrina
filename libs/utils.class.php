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
 * @category  User
 */
namespace Centrina\System\Libs;

class Utils {
	public static function getClientIP()
	{
		$ipaddress = "";

		if (isset($_SERVER["HTTP_CLIENT_IP"]))
			$ipaddress = $_SERVER["HTTP_CLIENT_IP"];
		else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if(isset($_SERVER["HTTP_X_FORWARDED"]))
			$ipaddress = $_SERVER["HTTP_X_FORWARDED"];
		else if(isset($_SERVER["HTTP_FORWARDED_FOR"]))
			$ipaddress = $_SERVER["HTTP_FORWARDED_FOR"];
		else if(isset($_SERVER["HTTP_FORWARDED"]))
			$ipaddress = $_SERVER["HTTP_FORWARDED"];
		else if(isset($_SERVER["REMOTE_ADDR"]))
			$ipaddress = $_SERVER["REMOTE_ADDR"];
		else
			$ipaddress = "UNKNOWN";

		return $ipaddress;
	}

	//With the benchmarks I took of all 4 this one was the fastest overall, just not the most unique, but we need speed more so anyways.
	public static function generateString($len)
	{
		$keys = array_merge(range(0,9), range('a', 'z'));

		$key = "";
		for($i=0; $i<$len; $i++) {
			$key .= $keys[mt_rand(0, count($keys) - 1)];
		}
		return $key;
	}

	public static function generateString3($len)
	{
		$char = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
				"R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i",
				"j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		$num = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

		$key = "";

		for($i=0; $i<$len; $i++)
		{
			$rl = "";
			if(rand()%100 <= 50)
				$rl = $char[rand(0, 51)];
			else
				$rl = $num[rand(0, 9)];

			$key = (string)$key.(string)$rl;
		}

		return $key;
	}

	public static function generateString2($len)
	{
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"; //the list of possible chars
		$key = ""; //our result

		for($i=0; $i<$len; $i++)
		{
			$key .= $chars[mt_rand( 0, strLen($chars)-1 )];
		}

		return $key;
	}

	public static function generateSecureStr($len)
	{
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*"; //the list of possible chars
		$key = ""; //our result

		for($i=0; $i<$len; $i++)
		{
			$key .= $chars[random_int( 0, strLen($chars)-1 )];
		}

		return $key;
	}

	//Original code by Xeoncross@Stack Overflow https://stackoverflow.com/questions/3770513/detect-browser-language-in-php
	public static function preferedLanguages(array $available_languages, $defaultLang = "en")
	{
		$langs;
		$httpLanguage = $_SERVER["HTTP_ACCEPT_LANGAUGE"];
		$available_languages = array_flip($available_languages);

		try {
			preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($httpLanguage), $matches, PREG_SET_ORDER);
		} catch(\Exception $e) {
			return $defaultLang; //I could make it trigger an error, but not wise if this code is gonna be used in a standard enviroment.
		}

		foreach($matches as $match)
		{
			list($a, $b) = explode('-', $match[1]) + array('', '');
			$value = isset($match[2]) ? (float) $match[2] : 1.0;

			if(isset($available_languages[$match[1]]))
			{
				$langs[$match[1]] = $value;
				continue;
			}

			if(isset($available_languages[$a]))
			{
				$langs[$a] = $value - 0.1;
			}
		}

		if($langs){
			arsonst($langs);
		}else{
			$langs[1] = $defaultLang;
		}

		return $langs;
	}

	public static function funcTest($func, &...$args)
	{
		$startTime = microtime();
		call_user_func_array($func, $args);
		$endTime = microtime();

		return $endTime - $startTime;
	}
}
?>
