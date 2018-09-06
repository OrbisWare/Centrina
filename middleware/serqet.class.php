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
 * @category Middleware
 */
namespace Centrina\System\Middleware;
use Centrina\System\Core\CT_Core as CT_Core;
use Centrina\System\Libs\Utils as Utils;

class Serqet {
	private $config;
	private $mysql;

	public function __construct($conn)
	{
		$this->config = CT_Core::loadConfig("config.php");

		if($conn->isConnected() === TRUE)
		{
			$this->mysql = $conn;
			ini_set("session.save_handler", "user");
			ini_set("session.use_only_cookies", 1);
			ini_set("session.cookie_httponly", 1);

			session_set_save_handler(
				array(&$this, "open"),
				array(&$this, "close"),
				array(&$this, "read"),
				array(&$this, "write"),
				array(&$this, "destroy"),
				array(&$this, "gc")
			);

			$cookieParams = session_get_cookie_params();
			session_set_cookie_params(
				$cookieParams["lifetime"],
				$cookieParams["path"],
				$cookieParams["domain"],
				$this->config["secure_session"],
				true
			);

			//session_start();
		}else{
			trigger_error("Session System: Unable to connect to database", E_USER_ERROR);
		}
	}

	public function startSession()
	{
		session_start();
	}

	public function destroySession()
	{
		$cookieParams = session_get_cookie_params();
		setcookie(
			session_name(),
			"",
			time() - 42000,
			$cookieParams["path"],
			$cookieParams["domain"],
			$cookieParams["secure"],
			$cookieParams["httponly"]
		);

		session_unset();
		session_destroy();
	}

	private function open()
	{
		return true;
	}

	private function close()
	{
		return true;
	}

	private function read($id)
	{
		$hash = hash("crc32", $_SERVER['HTTP_USER_AGENT'].Utils::getClientIP());
		$stmt = $this->mysql->prepare("SELECT `data` FROM ct_sessions WHERE `id` = ? AND `hash` = ? LIMIT 1");
		$stmt->bind_param("is", $id, $hash);
		$stmt->execute();
		$data = $stmt->fetch_all();

		return $data["data"] || "";
	}

	private function write($id, $data)
	{
		$time = time();
		$hash = hash("crc32", $_SERVER['HTTP_USER_AGENT'].Utils::getClientIP());
		$stmt = $this->mysql->prepare("INSERT INTO ct_sessions(`id`, `data`, `hash`,`expire`) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE `data` = VALUES(data), `expire` = VALUES(expire)");
		$stmt->bind_param("issi", $id, $data, $hash, $time);
		$stmt->execute();

		return true;
	}

	private function destroy($id)
	{
		$stmt = $this->mysql->prepare("DELETE FROM ct_sessions WHERE `id` = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->close();

		return true;
	}

	private function gc($lifetime)
	{
		$stmt = $this->mysql->prepare("DELETE FROM ct_sessions WHERE `expire` < ?");
		$stmt->bind_param("i", time() - $lifetime);
		$stmt->execute();
		$stmt->close();

		return true;
	}
}
?>
