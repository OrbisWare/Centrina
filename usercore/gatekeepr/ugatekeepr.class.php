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
namespace Centrina\System\UserCore\GateKeepr;
use Centrina\System\MySQL\Vaclarush as Vac;

class UGatekeepr {
  public static function hasPermission($perm, $userid = null)
  {
    if(is_null($userid))
      $userid = $_SESSION["user_id"];

    $conn = Vac::getDatabase();
    $gk = new Gatekeepr($conn, $userid);
    $gk->loadPermissions();
    return $gk->checkUserPerm($permid);
  }

  public static function hasRole($role, $userid = null)
  {
    if(is_null($userid))
      $userid = $_SESSION["user_id"];

    $stmt = Vac::getDatabase()->prepare("SELECT `role_id` FROM ct_userroles WHERE `user_id` = ? AND `role_id` = ?");
    $stmt->bind_param($userid, $role);
    $stmt->store_result();
    $rows = $stmt->num_rows;

    if($rows > 0)
      return TRUE;

    return FALSE;
  }
}
?>
