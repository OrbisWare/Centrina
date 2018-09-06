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
 * @category  Modules
 */
namespace Centrina\System\Modules;
use Centrina\System\Core\CT_Core as CT_Core;
use Centrina\System\Libs\Utils as Utils;
use Centrina\System\Libs\Vaclarush as Vac;
use Centrina\System\Libs\Murmur as Murmur;
use \Exception;

class Token {
	private $_curToken;
	private $_tokenTime;
	protected $_conn;
	protected $config;

	public function __construct($conn)
	{
		if($conn->isConnected())
		{
			$this->_conn = $conn;
			$this->config = CT_Core::loadConfig("config.php");
			$this->_tokenTime = $this->config["token_time"]; //Default time

			$conn->addCreationQueue(array(
				"CREATE TABLE ct_tokens (
					id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
					token VARCHAR(32) NOT NULL,
					time INTEGER(11) NOT NULL,
					user_ip INTEGER UNSIGNED NOT NULL,

					PRIMARY KEY(id)
				);"
			));
		}else{
			throw new Exception("Token System: Unable to establish connection to via database.");
			return;
		}
	}

	protected function generate()
	{
		$len = $this->config["token_length"];
		$str = Utils::generateString($len);
		$str = Murmur::hash3($str);
		$this->_curToken = $str;

		return $str;
	}

	public function setTime($time)
	{
		$this->_tokenTime = $time;
	}

	public function create($userip, $time = null)
	{
		$token = $this->generate();
		$userip = ip2long( htmlspecialchars($userip) ); //Your IP shouldn't change while verifing so we lock tokens to an IP.
		$time = $time + time();

		$stmt = $this->_conn->prepare("INSERT INTO ct_tokens(token, time, user_ip) values(?, ?, ?)");
		$stmt->bind_param("sii", $token, $time, $userip);
		$stmt->execute();
		$stmt->close();
	}

	public function get()
	{
		return $this->_curToken;
	}

	public static function checkToken($token)
	{
		Token::deleteOldTokens(); //Clean out the database of old tokens.
		$userip = ip2long(Utils::getClientIP());

		$stmt = Vac::getDatabase()->prepare("SELECT `time` FROM ct_tokens WHERE `token` = ? AND `user_ip` = ? LIMIT 1");
		$stmt->bind_param("si", $token, $userip);
		$stmt->execute();
		$data = $stmt->fetch_array();

		if(is_empty($data))
		{
			return false; //We can't find that token.
		}

		$time = $data["time"];
		$curTime = time();
		if($curTime < $time)
		{
			return true; //not expired
		}else{
			return false; //expired
		}
	}

	public static function deleteOldTokens()
	{
		$time = time();

		$stmt = Vac::getDatabase()->prepare("DELETE FROM ct_tokens WHERE `time` < ?");
		$stmt->bind_param("i", $time);
		$stmt->execute();
		$stmt->close();
	}
}
?>
