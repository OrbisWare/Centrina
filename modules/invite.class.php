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
use \Exception;

class Invite {
	private $_curInvite;
	protected $_conn;
	protected $config = CT_Core::loadConfig("config.php")

	public function __construct($conn)
	{
		if($conn->isConnected())
		{
			$this->_conn = $conn;

			$conn->addCreationQueue(array(
				"CREATE TABLE ct_invites (
					token VARCHAR(32) NOT NULL,
					time INTEGER(11) NOT NULL,
          curTime INTEGER(11) NOT NULL,
          user_id INTEGER UNSIGNED NOT NULL,
          used TINYINT(1) NOT NULL,

          PRIMARY KEY(token),
					FOREIGN KEY(user_id) REFERENCES ct_users(id)
				);"
			));
		}else{
			throw new Exception("Invite System: Unable to establish connection to via database.");
			return;
		}
	}

	protected function generateInvite()
	{
		$len = $this->config["invite_length"];
		$str = Utils::generateString($len);

		//A poor attempt at making this system future proof...
		if($this->isTokenUsed($str) === TRUE) //Check if the generated token already exist.
			$this->generateInvite();
		else
			$this->_curInvite = $str;
	}

	public function createInvite($userid)
	{
		$invite = $this->generateInvite();
    $cTime = $this->config["invite_time"];
    $curTime = time();

    if($cTime === 0){
		  $time = $cTime;
    }else{
      $time = $curTime + $cTime;
    }

		$stmt = $this->_conn->prepare("INSERT INTO ct_invites(token, time, curTime, user_id) values(?, ?, ?, ?)");
		$stmt->bind_param("siii", $invite, $time, $curTime, $userid);
		$stmt->execute();
		$stmt->close();
	}

  protected function isTokenUsed($token)
	{
		$stmt = $this->_conn->prepare("SELECT `token` FROM ct_invites WHERE `token` = ? LIMIT 1");
		$stmt->bind_param("s", $token);
		$stmt->execute();
		$data = $stmt->fetch_array();

		if(empty($data))
			return true;
	}

	public function checkInvite()
	{
		$invite = $this->_curInvite;

		$stmt = $this->_conn->prepare("SELECT `time`, `curTime`, `used` FROM ct_invites WHERE `token` = ? LIMIT 1");
		$stmt->bind_param("s", $invite);
		$stmt->execute();
		$data = $stmt->fetch_array();

    $ivTime = $data["time"];
		$ivCreation = $data["curTime"];
    $ivUsed = $data["used"];
    $curTime = time();

		if($ivTime !== 0 || $ivTime < $curTime)
    {
			$this->deleteOldInvites();
      return false //The invite code is expired.
    }

    if($ivUsed === 1)
    {
			$this->deleteOldInvites();
      return false; //The invite code as been used already
    }

		return true;
	}

  public function deleteOldInvites()
	{
		$time = time();

		$stmt = $this->_conn->prepare("DELETE FROM ct_invites WHERE `time` < ?");
		$stmt->bind_param("i", $time);
		$stmt->execute();
		$stmt->close();
	}

	public function getInviteCode()
	{
		return $this->_curInvite;
	}
}
?>
