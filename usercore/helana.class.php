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
namespace Centrina\System\UserCore;
use Centrina\System\MySQL\Vaclarush as Vaclarush;
use Centrina\System\Libs\Vivian as Vivian;

class Helana {
	public static function banUser()
	{
		$mysql = Vaclarush::getDatabase();
		$stmt = $mysql->prepare("INSERT INTO ct_bans(`user_id`, `user_ip`, `reason`, `startTime`, `endTime`, `admin`) VALUES(?, ?, ?, ?, ?, ?)");
	}

	public static function unbanUser($userid)
	{
		$mysql = Vaclarush::getDatabase();
	}

	public static function isBanned()
	{
		$mysql = Vaclarush::getDatabase();
	}

	public static function isLoggedIn()
	{
		if( isset($_SESSION["user_id"], $_SESSION["user_string"]) )
		{
			$mysql = Vaclarush::getDatabase();
			$agent = $_SERVER["HTTP_USER_AGENT"];
			$userid = $_SESSION["user_id"];
			$user_string = $_SESSION["user_string"];

			$stmt = $mysql->prepare("SELECT `password` FROM users WHERE `id` = ? LIMIT 1");
			$stmt->bind_param($userid);
			$stmt->execute();
			$stmt->bind_result($password);
			$stmt->fetch();

			if($stmt->num_rows == 1)
			{
				$hash = Vivian::smartHash($password . $agent);
				$check = Vivian::hashCompare($hash, $user_string);
				return $check[1];
			}
		}
	}
}
?>
