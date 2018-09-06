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
 * @category  Middleware
 */
namespace Centrina\System\Middleware;

class Nephthys {
	public static function filterPOST($post, $type)
	{
		switch($type)
		{
			case "string":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_STRING);
				return $post;
			case "int":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_INT);
				return $post;
			case "email":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_EMAIL);
				return $post;
			case "float_dec":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				return $post;
			case "float_thou":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
				return $post;
			case "float_sci":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
				return $post;
			case "url":
				$post = filter_input(INPUT_POST, $post, FILTER_SANITIZE_URL);
				return $post;
			case "utf-8":
				$post = htmlspecialchars($_POST[$post], ENT_QUOTES, "UTF-8");
				return $post;
			default:
				return $_POST[$post];
		}
	}

	public static function filterGET($get, $type)
	{
		switch($type)
		{
			case "string":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_STRING);
				return $get;
			case "int":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_INT);
				return $get;
			case "email":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_EMAIL);
				return $get;
			case "float_dec":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				return $get;
			case "float_thou":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
				return $get;
			case "float_sci":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
				return $get;
			case "url":
				$get = filter_input(INPUT_GET, $get, FILTER_SANITIZE_URL);
				return $get;
			case "utf-8":
				$get = htmlspecialchars($_GET[$get], ENT_QUOTES, "UTF-8");
				return $get;
			default:
				return $_GET[$get];
		}
	}

	public static function filterVar($var, $type)
	{
		switch($type)
		{
			case "string":
				$var = filter_var($var, FILTER_SANITIZE_STRING);
				return $var;
			case "int":
				$var = filter_var($var, FILTER_SANITIZE_NUMBER_INT);
				return $var;
			case "email":
				$var = filter_var($var, FILTER_SANITIZE_EMAIL);
				return $var;
			case "float_dec":
				$var = filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				return $var;
			case "float_thou":
				$var = filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
				return $var;
			case "float_sci":
				$var = filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
				return $var;
			case "url":
				$var = filter_var($var, FILTER_SANITIZE_URL);
				return $var;
			case "utf-8":
				$var = htmlspecialchars($var, ENT_QUOTES, "UTF-8");
				return $var;
			case "ipv4":
				$var = filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
				return $var;
			case "ipv6":
				$var = filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
				return $var;
			case "mac":
				$var = filter_var($var, FILTER_VALIDATE_MAC);
				return $var;
			default:
				return $var;
		}
	}
}