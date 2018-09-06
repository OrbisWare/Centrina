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

class Validator{
	protected static $regex = array(
		"date" => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$",
		"phone" => "^[0-9]{10,11}\$",
		"zipcode" => "^[1-9][0-9]{3}[a-zA-Z]{2}\$",
		"price" => "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$",
	);

	public static function filterPOST($post, $type)
	{
		switch($type)
		{
			case "string":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_STRING);
				return $val;
			case "int":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_INT);
				return $val;
			case "email":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_EMAIL);
				return $val;
			case "float_dec":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				return $val;
			case "float_thou":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
				return $val;
			case "float_sci":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
				return $val;
			case "url":
				$val = filter_input(INPUT_POST, $post, FILTER_SANITIZE_URL);
				return $val;
			case "utf-8":
				$val = htmlspecialchars($_POST[$post], ENT_QUOTES, "UTF-8");
				return $val;
		}
	}

	public static function filterGET($get, $type)
	{
		switch($type)
		{
			case "string":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_STRING);
				return $val;
			case "int":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_INT);
				return $val;
			case "email":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_EMAIL);
				return $val;
			case "float_dec":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				return $val;
			case "float_thou":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
				return $val;
			case "float_sci":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
				return $val;
			case "url":
				$val = filter_input(INPUT_GET, $get, FILTER_SANITIZE_URL);
				return $val;
			case "utf-8":
				$val = htmlspecialchars($_GET[$get], ENT_QUOTES, "UTF-8");
				return $val;
		}
	}

	public static function filterVar($var, $type)
	{
		switch($type)
		{
			case "string":
				$val = filter_var($var, FILTER_SANITIZE_STRING);
				return $val;
			case "int":
				$val = filter_var($var, FILTER_SANITIZE_NUMBER_INT);
				return $val;
			case "email":
				$val = filter_var($var, FILTER_SANITIZE_EMAIL);
				return $val;
			case "float_dec":
				$val = filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				return $val;
			case "float_thou":
				$val = filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
				return $val;
			case "float_sci":
				$val = filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
				return $val;
			case "url":
				$val = filter_var($var, FILTER_SANITIZE_URL);
				return $val;
			case "utf-8":
				$val = htmlspecialchars($var, ENT_QUOTES, "UTF-8");
				return $val;
		}
	}

	public static function htmlPurify($input)
	{
		$input = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $input);
		$input = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $input);
		$input = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $input);
		$input = html_entity_decode($input, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$input = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $input);

		// Remove javascript: and vbscript: protocols
		$input = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $input);
		$input = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $input);
		$input = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $input);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $input);
		$input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $input);
		$input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $input);

		return $input;
	}

	public static function validate($data, array $rules)
	{
		$regex = self::$regex;

		foreach($rules as $key => $rule)
		{
			switch($key)
			{
				case "min":
					if(strlen($data) < $rule)
					{
						return false;
					}
					break;
				case "max":
					if(strlen($data) > $rule)
					{
						return false;
					}
					break;
				case "int":
					if(!is_int($data))
					{
						return false;
					}
					break;
				case "bool":
					if(!is_bool($data))
					{
						return false;
					}
					break;
				case "string":
					if(!is_string($data))
					{
						return false;
					}
					break;
				case "float":
					if(!is_float($data))
					{
						return false;
					}
					break;

				case "email":
					$ret = filter_var($data, FILTER_VALIDATE_EMAIL);
					return $ret;
				case "ipv4":
					$ret = filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
					return $ret;
				case "ipv6":
					$ret = filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
					return $ret;
				case "mac":
					$ret = filter_var($data, FILTER_VALIDATE_MAC);
					return $ret;
				case "url":
					$ret = filter_var($data, FILTER_VALIDATE_URL);
					return $ret;

				case "date":
					$ret = preg_match($regex[0], $data);
					if($ret === false)
					{
						return false;
					}
					break;
				case "phone":
					$ret = preg_match($regex[1], $data);
					if($ret === false)
					{
						return false;
					}
					break;
				case "zipcode":
					$ret = preg_match($regex[2], $data);
					if($ret === false)
					{
						return false;
					}
					break;
				case "price":
					$ret = preg_match($regex[3], $data);
					if($ret === false)
					{
						return false;
					}
					break;
			}
		}
	}
}
?>
