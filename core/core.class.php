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
use Centrina\System\Core\Autoload;

class Core {
	private $cores;
	private $autoload;

	public function __construct()
	{
		$this->cores = array(
			0 => true, //Systems core
			1 => false, //User core
			2 => false, //PHPMailer
			3 => false, //Gatekeepr
			4 => false //Modules
		);
		$this->autoload = new Autoload();
		$this->autoload->register();
	}

	public function load()
	{
		$classes = array(
			"SQLObject" => "Centrina\System\MySQL",
			"DBObject" => "Centrina\System\MySQL",
			"Vaclarush" => "Centrina\System\MySQL",
			"Serqet" => "Centrina\System\Middleware",
			"Validator" => "Centrina\System\Middleware",
			"FileIO" => "Centrina\System\Libs",
			"Utils" => "Centrina\System\Libs",
			"Vivian" => "Centrina\System\Libs",
		);

		//Backwards compatibility for versions lower than 7.0.0
		if( version_compare(PHP_VERSION, "7.0.0", "<") )
		{
			require("compat/random.php");
		}
		require("meta.class.php");
		require("hooks.class.php");
		require("router.class.php");
		require("autoload.class.php");

		//case statement here

		$this->autoload->SetClassFiles(array(
			"SQLObject" => "Centrina\System\MySQL",
			"DBObject" => "Centrina\System\MySQL",
			"Vaclarush" => "Centrina\System\MySQL",
			"Serqet" => "Centrina\System\Middleware",
			"Validator" => "Centrina\System\Middleware",
			"FileIO" => "Centrina\System\Libs",
			"Utils" => "Centrina\System\Libs",
			"Vivian" => "Centrina\System\Libs",
		));
		$this->autoload->loadClasses();
	}

	public function initUserCore()
	{
		$this->cores[1] = true;
		$this->autoload->setClassFiles(array(
			"User" => "Centrina\System\UserCore",
			"Register" => "Centrina\System\UserCore",
			"Login" => "Centrina\System\UserCore",
			"Helana" => "Centrin\System\UserCore",
			"UHelper" => "Centrina\System\UserCore",
		));
	}

	public function initPHPMailer()
	{
		$this->cores[2] = true;
		$this->autoload->setClassFiles(array(
			"Exception" => "Centrina\System\Libs\PHPMailer",
			"PHPMailer" => "Centrina\System\Libs\PHPMailer",
			"POP3" => "Centrina\System\Libs\PHPMailer",
			"SMTP" => "Centrina\System\Libs\PHPMailer",
		));
	}

	public function initGatekeepr()
	{
		$this->cores[3] = true;
		$this->autoload->setClassFiles(array(
			"Gatekeepr" => "Centrina\System\UserCore\Gatekeepr",
			"UGatekeepr" => "Centrins\System\UserCore\Gatekeepr",
		));
	}

	public function initModules()
	{
		$this->cores[4] = true;
		$this->autoload->setClassFiles(array(
			"Token" => "Centrina\System\Modules",
			"Invite" => "Centrina\System\Modules",
		));
	}
}
?>
