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

class Router {
	private $_routes = array();
	private $_routeErrors = array();
	private $_routeFuncs = array();
	private $_defaultErr;

	private $matchTypes = array(
		"i" => "[0-9]++", //integer
		"s" => "[0-9A-Za-z]++", //string
		"*" => "[^,]+" //anything
	);

	public function __construct($error)
	{
		$this->_defaultErr = $error;
	}

	private function doCallback($callback)
	{
		if( is_callable($callback) )
			call_user_func($callback);
		else
			echo $callback;
	}

	private function doError($key)
	{
		$error = $this->_routeErrors[$key] ?: $this->_defaultErr;

		if( is_callable($error) )
			call_user_func($error);
		else
			echo $error;
	}

	private function compileRegex($route)
	{
		$newRoute = $route;
		$parses = explode("/", $route);

		if( preg_match_all("/{(.*?)}/", $route, $matches) )
		{
			$texts = $matches[0];
			$results = array();

			for($part = 0; $part < count($parses); $part++)
			{
				for($i = 0; $i < count($texts); $i++)
				{
					if($texts[$i] == $parses[$part])
					{
						$strs = explode( ":", trim($texts[$i], "{}") );
						if($strs)
						{
							$var = $strs[0];
							$regex = $strs[1];

							$newVar = "?'".$var."'";
							$newRegex = str_replace($regex, $this->matchTypes[$regex], $regex);
							$results[] = "(".$newVar.$newRegex.")";
						}else{
							$newRegex = str_replace($texts[$i], $this->matchTypes[$texts[$i]], $regex);
							$results[] = "(".$newRegex.")";
						}

						$parses[$part] = str_replace($texts[$i], $results[$i], $parses[$part]);
					}
				}
			}

			return implode("/", $parses);
		}else{
			return $route;
		}
	}

	public function addRoute($regex, $func, $error = null)
	{
		$this->_routes[] = $regex;
		$this->_routeFuncs[] = $func;
		$this->_routeErrors[] = $error;
	}

	public function dispatch()
	{
		$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), "/" );
		$uri = trim( str_replace( $uri, "", $_SERVER["REQUEST_URI"] ), "" );
		$uri = urldecode($uri);

		foreach($this->_routes as $key => $value)
		{
			if( $value == $uri )
			{
				return $this->doCallback($this->_routeFuncs[$key]);
			}
			elseif( preg_match("~^".$this->compileRegex($value)."$~i", $uri, $params) )
			{
				$_GET = $params;
				return $this->doCallback($this->_routeFuncs[$key]);
			}
			elseif( is_array($value) )
			{
				for($i = 0; $i < count($value); $i++)
				{
					if( $value[$i] == $uri )
					{
						return $this->doCallback($this->_routeFuncs[$key]);
					}
					elseif( preg_match("~^".$this->compileRegex($value[$i])."$~i", $uri, $params) )
					{
						$_GET = $params;
						return $this->doCallback($this->_routeFuncs[$key]);
					}
				}
			}
		}
	}
}
?>
