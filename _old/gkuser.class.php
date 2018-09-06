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
 * @category  User
 */
namespace Centrina\System\User\Gatekeeper;

class GKUser extends User {
  public static function isLoggedIn()
  {
    $hash_algorithm = $this->config["hash_algorithm"];
		$login_cookie = $this->config["login_cookie"];

    if( isset($_SESSION["user_id"], $_SESSION["user_string"]) )
    {
      $user_browser = $_SERVER["HTTP_USER_AGENT"];
			$user_id = $_SESSION["user_id"];
      $user_string = $_SESSION["user_string"];

      $stmt = $GLOBALS["ct_db"]->prepare("SELECT `password` FROM ct_user WHERE `id` = ?");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->fetch();
      $stmt->close();

      $user_check = hash($hash_algorithm, $result . $user_browser);

      if($user_check == $user_string)
        return true

    }else{
      if( isset($_COOKIE[$login_cookie]) )
      {
        $cookie = $_COOKIE[$login_cookie];
        $arr = explode(",", $cookie);
        $username = $arr[0];
        $password = $arr[1];

        $stmt = $GLOBALS["ct_db"]->prepare("SELECT `id` FROM ct_users WHERE `username` = ? AND `password` = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $data = $stmt->fetch();
        $stmt->close();

        $user_id = $data["id"];

        if(count($data) > 0)
        {
          $user_browser = $_SERVER["HTTP_USER_AGENT"];
					$_SESSION["user_id"] = $user_id;
					$_SESSION["username"] = $username;
					$_SESSION["user_string"] = hash($hash_algorithm, $password . $user_browser);
        }
      }
    }
  }

  public static function getRole($user_id)
  {

  }

  public static function getPermissions($user_id)
  {
    
  }

  public static function checkUserPermission($user_id)
  {

  }

  public static function checkRolePermission($user_id)
  {

  }

  public static function checkPermission($user_id)
  {

  }

  public static function removeRole($user_id, $role_id)
  {

  }

  public static function removePermission($user_id, $perm_id)
  {

  }

  public static function addRole($user_id, $role_id)
  {

  }

  public static function addPermission($user_id, $perm_id)
  {

  }
}
?>
