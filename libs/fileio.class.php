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
 * @category  Libs
 */
namespace Centrina\System\Libs;
use \Exceptions;

class FileIO {
  protected $handle;
  protected $filePath;
  protected $types = array(
	"read" => "r",
	"write" => "a",
	"overwrite" => "w"
  );

  public function __construct($file, $ty)
  {
	$type = $this->types[$ty];

	if(!isset($type))
	{
		trigger_error("Attempted to open file with unsupported file type:".$ty, E_USER_NOTICE);
	}

	if(is_dir($file))
	{
		trigger_error("Unable to open file: ".$file." is a directory.", E_USER_NOTICE);
	}

	switch($ty)
	{
	  case "read":
		if(is_readable($file))
		{
			trigger_error("Unable to read file due to incorrect permissions: ".$file, E_USER_NOTICE);
		}
		break;
	  case "write":
		if(is_writeable($file))
		{
			trigger_error("Unable to write to file due to incorrect permissions: ".$file, E_USER_NOTICE);
		}
		break;
	  case "overwrite":
		if(is_writeable($file))
		{
			trigger_error("Unable to write to file due to incorrect permissions: ".$file, E_USER_NOTICE);
		}
		break;
	}

	$this->filePath = $file;
	$this->handle = fopen($file, $type);
	if(FALSE === $this->handle)
	{
		trigger_error("Failed to open file: ".$file.":".$ty, E_USER_ERROR);
	}
  }

  public function fileWrite($data)
  {
	fwrite($this->handle, $data);
	touch($this->filePath);
  }

  public function readFile($bytes)
  {
	return fread($this->handle, $bytes);
  }

  public function readContent()
  {
	return fread($this->handle, filesize($this->filePath));
  }

  public function fileClose()
  {
	fclose($this->handle);
	$this->handle = null;
	$this->filePath = null;
	unset($this); //we destroy the object since we no longer need it when file is closed.
  }
}
 ?>
