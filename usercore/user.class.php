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
 * @since     Version 1.1 - Moved all non-session functions to uhelper.class.php
 *
 * @category  User
 */
namespace Centrina\System\UserCore;
use \Exception;
use Centrina\System\Core\CT_Core as CT_Core;
use Centrina\System\Libs\Vivian as Vivian;
use Centrina\System\Libs\Murmur as Murmur;

/*
	02 - no email supplied
	03 - no password supplied
	04 - email is taken
	05 - username is taken
	06 - incorrect login
	07 - to many login attempts, try again later
	08 - account not verified.
*/

class User {
	protected $_conn;
	protected $loggedUsers;
	protected $config;
	private $_error;

	protected $_usrID; //We actually need this acceable from the object inherited from this one.

	public function __construct($conn)
	{
		$this->_error = null;
		$this->loggedUsers = array();
		$this->config = CT_Core::loadConfig("config.php");

		if(session_status() == PHP_SESSION_NONE)
		{
			trigger_error("User Class: No session started, you done fucked up", E_USER_ERROR);
		}

		if($conn->isConnected())
		{
			$this->_usrID = $_SESSION["user_id"];

			$this->_conn = $conn;
			$conn->addCreationQueue(array(
				"CREATE TABLE ct_users (
				  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  username VARCHAR(255) NOT NULL,
				  email  VARCHAR(255) NOT NULL,
				  password VARCHAR(130) NOT NULL,
				  salt VARCHAR(128) NOT NULL,
					browser VARCHAR(32) NOT NULL,
				  time INTEGER(11) NOT NULL,
					last_online INTEGER(11) NOT NULL,
					ip INTEGER UNSIGNED NOT NULL,
					verified TINYINT(1) NOT NULL,
					eauth TINYINT(1) NOT NULL,

				  PRIMARY KEY (id)
				);",
				"CREATE TABLE ct_loginattempts (
					user_id INTEGER UNSIGNED NOT NULL,
					time INTEGER(11) NOT NULL,
					user_ip INTEGER UNSIGNED NOT NULL,

					FOREIGN KEY(user_id) REFERENCES ct_users(id)
				);",
				"CREATE TABLE ct_roles (
					id VARCHAR(255) NOT NULL,
					rdesc TEXT NOT NULL,

				  PRIMARY KEY(id)
				);",
				"CREATE TABLE ct_permissions (
					id VARCHAR(255) NOT NULL,
					pdesc TEXT NOT NULL,

				  PRIMARY KEY(id)
				);",
				"CREATE TABLE ct_roleperms (
				  role_id VARCHAR(255) NOT NULL,
				  perm_id VARCHAR(255) NOT NULL,

					FOREIGN KEY(role_id) REFERENCES ct_roles(id),
					FOREIGN KEY(perm_id) REFERENCES ct_permissions(id)
				);",
				"CREATE TABLE ct_userroles (
					user_id INTEGER UNSIGNED NOT NULL,
					role_id VARCHAR(255) NOT NULL,

					FOREIGN KEY(user_id) REFERENCES ct_users(id),
					FOREIGN KEY(perm_id) REFERENCES ct_roles(id)
				);",
				//I'm not worried about user permissions yet.
				/*"CREATE TABLE ct_userperms (
					user_id INTEGER UNSIGNED NOT NULL,
					perm_id INTEGER UNSIGNED NOT NULL,

					FOREIGN KEY(user_id) REFERENCES ct_users(id),
					FOREIGN KEY(perm_id) REFERENCES ct_permissions(id)
				);"*/
			));

		}else{
			throw new \Exception("User System: Unable to find via connection to via database.");
			return;
		}
	}

	public function updatePassword($pass)
	{
		$random_id = uniqid( mt_rand( 1, mt_getrandmax() ), true );
		$salt = Vivian::smartHash($random_id, false);
		$password = Vivian::smartHash($pass . $salt);

		$stmt = $this->_conn->prepare("UPDATE ct_users SET `password` = ? WHERE `id` = ?");
		$stmt->bind_param("si", $password, $this->_usrID);
		$stmt->execute();
		$stmt->close();
	}

	public function updateEmail($email)
	{
		$stmt = $this->_conn->prepare("UPDATE ct_users SET `email` = ? WHERE `id` = ?");
		$stmt->bind_param("si", $email, $this->_usrID);
		$stmt->execute();
		$stmt->close();
	}

	public function updateLastOnline($time = null)
	{
		if(is_null($time))
			$time = time();

		$userip = ip2long(Utils::getClientIP());
		$stmt = $this->_conn->prepare("UPDATE ct_users SET `last_online` = ?, `user_id` = ? WHERE `id` = ?");
		$stmt->bind_param("iii", $time, $userip, $this->_usrID);
		$stmt->execute();
		$stmt->close();
	}

	public function updateBrowser($str = $_SERVER["HTTP_USER_AGENT"])
	{
		$browser = Murmur::hash3($str);

		$stmt = $this->_conn->prepare("UPDATE ct_users SET `browser` = ? WHERE `id` = ?");
		$stmt->bind_param("si", $browser, $this->_usrID);
		$stmt->execute();
		$stmt->close();
	}

	public function updateIP()
	{
		$userip = ip2long(Utils::getClientIP());
		$stmt = $this->_conn->prepare("UPDATE ct_users SET `ip` = ? WHERE `id` = ?");
		$stmt->bind_param("ii", $userip, $this->_usrID);
		$stmt->execute();
		$stmt->close();
	}

	public function updatePassword($password)
	{
		UHelper::forceChangePassword($this->_usrID, $password);
	}

	public function getUsername()
  {
    return $_SESSION["username"];
  }

  public function getEmail()
  {
		return $_SESSION["email"];
  }

	protected function error($err)
	{
		Hooks::call("UserError", $err);
		$this->_error = $err;
	}

	public function getError()
	{
		return $this->_error;
	}
}
?>
