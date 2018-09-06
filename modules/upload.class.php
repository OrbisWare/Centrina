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
 * @category  Modules
 */
namespace Centrina\System\Modules;

class Upload {
	protected $_conn;
	protected $_error;
	protected $_userid;
	protected $_whiteList;

	public function __construct($conn, $userid, $whitelist)
	{
		if($conn->isConnected())
		{
			$this->_conn = $conn;
			$this->_userid = $userid;

			if( is_array($whitelist) )
			{
				$this->_whiteList = $whitelist;
				throw new Exception("Upload Class: whitelist isn't an array.");
			}
		}else{
			throw new Exception("Unable to connect to MySQL server.");
		}
	}

	public function secureFileUpload($file, $location)
	{
		$file_name;
		$file_type;

		if(  )
		{
			$newFileName = Centrina\Core\Classes\Utils::generateRandomString().sha1($file_name);
			$moveLoc = sprintf($location."%s.%s", $newFileName, $file_type);

			$handle = fopen($moveLoc, "w");
			if($handle)
			{
				fwrite($handle, $file);
				fclose($handle);

				$stmt = $this->_conn->prepare("INSERT INTO");
				$stmt->bind_param();
				$stmt->execute();
				$stmt->close();
			}else{
				$this->_error = 13;
				return;
			}
		}else{
			$this->_error = 12;
			return;
		}
	}

	public function fileUpload($file, $location)
	{

	}

	public function avatarUpload($file, $location)
	{

	}

	public function getErrorID()
	{
		return $this->_error;
	}
}
?>
