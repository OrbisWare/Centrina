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
use \Exception;
use Cenrtina\System\Core\CT_Core as CT_Core;
use Centrina\System\Libs\FileIO;

class Log {
	protected $logPath;
	protected $date;
	protected $type;
	protected $_conn;
	protected $config;

	protected $handle;
	protected $levels = array(
		1 => "INFO",
		2 => "ERROR",
		3 => "DEBUG"
	);

	public function __construct($conn = null)
	{
		$this->config = CT_Core::loadConfig("config.php");
		$this->type = $config["log_type"];

		if($this->type == "mysql")
		{
			$this->constructMysql($conn);
		}else{
			$this->constructFile();
		}
	}

	private function constructMysql($conn)
	{
		if($conn->isConnected() === TRUE)
		{
			$this->_conn = $conn;
			$conn->addCreationQueue(array(
				"CREATE TABLE ct_log (
				  	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  	time INTEGER(11) NOT NULL,
				  	type VARCHAR(10) NOT NULL,
					message text NOT NULL,

				  	PRIMARY KEY (id)
				);"
			));

		}else{
			throw new Exception("Log System: Unable to find via connection to via database.");
			return;
		}
	}

	private function constructFile()
	{
		$this->logPath = $config["log_path"];
		$this->dateTime = $config["datetime_format"];
		$this->date = $config["date_format"];
		try{
			$this->handle = new FileIO($this->logPath, "write");
		}catch(Exception $e){
			echo "Critical Error: ".$e->getLine()."/".$e->getFile().": <b>".$e->getMessage()."</b>\n";
			die();
		}
	}

	public function writeLog($level, $msg)
	{
		if(!$this->levels[$level])
		{
			throw new Exception("Log System: Unable to find log type:	".$level);
			return;
		}

		if($this->type == "file")
		{
			$filepath = $this->logPath."log-".date($this->date).".txt";
			$message = $this->levels[$level]."-".date($this->dateTime)." --> ".$msg."\n";

			$this->handle->fileWrite($message);
			$this->handle->fileClose();
		}else{
			$ct = time();

			$stmt = $this->_conn->prepare("INSERT INTO ct_log(`time`, `type`, `message`) VALUES(?, ?, ?)");
			$stmt->bind_param("iss", $ct, $level, htmlspecialchars($msg));
			$stmt->execute();
			$stmt->close();
		}
	}
}
?>
