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
 * @category  Core
 */
namespace Centrina\System\Core;

class Autoload {
	protected $prefixes;
	protected $loaded;
	protected $classFiles;

	public function __construct()
	{
		$this->prefixes = array();
		$this->loaded = array();
		$this->classFiles = array();
	}

	public function register($prepend = false)
	{
		spl_autoload_register(array($this, "loadClasses"), true, $prepend);
	}

	public function unregister()
	{
		spl_autoload_unregister(array($this, "loadClasses"));
	}

	public function setClassFile($class, $file)
	{
		$this->classFiles[$class] = $file;
	}

	public function setClassFiles(array $classfiles)
	{
		$this->classFiles = $classfiles;
	}

	public function getClassFiles()
	{
		return $this->classFiles;
	}

	public function getLoadedClasses()
	{
		return $this->loaded;
	}

	public function loadClasses()
	{
		foreach($this->classFiles as $class => $namespace)
		{
			$pos = strpos($namespace, "\\");
			$lastPos = strpos( substr($namespace, 0, $pos + 1), "\\" );
			$filePath = substr($namespace, $lastPos, strlen($namespace));
			$filePath = str_replace("\\", "/", $filePath)."/";

			$file = $this->loadFile($class, $filePath);
		}

		return false;
	}

	public function loadClass($class)
	{
		if(isset($this->classFiles[$class]) === TRUE)
		{
			$namespace = $this->classFiles[$class];
			$pos = strpos($namespace, "\\");
			$lastPos = strpos( substr($namespace, 0, $pos + 1), "\\" );
			$filePath = substr($namespace, $lastPos, strlen($namespace));
			$filePath = str_replace("\\", "/", $filePath)."/";

			$file = $this->loadFile($class, $filePath);
			return $file;
		}

		return FALSE;
	}

	protected function loadFile($class, $filePath)
	{
		$suffix = ".class.php";
		$filePath = trim($filePath);
		$filePath = strtolower($filePath);
		$filePath = CT_ROOT.$filePath.$class.$suffix;
		if( $this->requireFile($filePath) === TRUE)
		{
			$this->loaded[$class] = $filePath;
			return $filePath;
		}
	}

	protected function requireFile($file)
	{
		if(is_readable($file) === TRUE)
		{
			require_once($file);
			return TRUE;
		}

		return FALSE;
	}
}
?>
