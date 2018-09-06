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
 * @category  User
 */
namespace Centrina\System\UserCore;
use Centrina\System\MySQL\Vaclarush as Vac;
use Centrina\System\Middleware\Serqet as Serqet;
use Centrina\System\Libs\Murmur as Murmur;
use Centrina\System\Libs\Utils as Utils;
use Centrina\System\Libs\Vivian as Vivian;

class UHelper {
  public static function getRegisterDate($userid = null)
  {
    if(is_null($userid))
      $userid = $_SESSION["user_id"];

    $stmt = Vac::getDatabase()->prepare("SELECT `time` FROM ct_users WHERE `id` = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $data = $stmt->fetch_all();

    return date("Y/m/d H:i:s", $data["time"]);
  }

  public static function verifyUser($userid, $bool = true)
  {
    $stmt = Vac::getDatabase()->prepare("UPDATE ct_users SET `verified` = ? WHERE `id` = ?");
		$stmt->bind_param("ii", $bool, $userid);
		$stmt->execute();
		$stmt->close();
  }

  public static function enableAuth($userid, $bool = true)
  {
    $stmt = Vac::getDatabase()->prepare("UPDATE ct_users SET `eauth` = ? WHERE `id` = ?");
		$stmt->bind_param("ii", $bool, $userid);
		$stmt->execute();
		$stmt->close();
  }

  public static function forceChangePassword($userid, $password)
  {
    $salt = self::generateSalt(); //we want to generate a totally new salt for any password changes.
    $password = Vivian::smartHash($password . $salt);

    $stmt = Vac::getDatabase()->prepare("UPDATE ct_users SET `password` = ?, `salt` = ? WHERE `id` = ?");
		$stmt->bind_param("ssi", $password, $salt, $userid);
		$stmt->execute();
		$stmt->close();
  }

  public static function generateSalt()
  {
    $salt = uniqid( mt_rand( 1, mt_getrandmax() ), true );
    $salt = Vivian::smartHash($salt, false);
    return $salt;
  }

  public static function logout()
  {
    $session = new Serqet(Vac::getDatabase()); //I use the custom session handler because it's more secure this way.

    if(session_status() == PHP_SESSION_NONE)
      $session->startSession(); //A session has to be started to destroy one.

    $session->destroySession();
  }
}
?>
