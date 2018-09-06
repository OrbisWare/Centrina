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

class Role {
  //roles management
  public static function createRole($name, $desc)
  {
    $stmt = $GLOBALS["ct_db"]->prepare("INSERT INTO ct_roles(`name`, `desc`) VALUES(?, ?)");
    $stmt->bind_param("ss", $name, $desc);
    $stmt->execute();
    $stmt->close();
  }

  public static function deleteRole($id)
  {

  }

  //permission management
  public static function createPermission($name, $desc)
  {
    $stmt = $GLOBALS["ct_db"]->prepare("INSERT INTO ct_permissions(`name`, `desc`) VALUES(?, ?)");
    $stmt->bind_param("ss", $name, $desc);
    $stmt->execute();
    $stmt->close();
  }

  public static function deletePermission($id)
  {

  }

  //permission assignment
  public static function assignPermission($role_id, $perm_id)
  {

  }

  public static function unassignPermission($role_id, $perm_id)
  {

  }

  public static function listRolePermissions($role_id)
  {

  }
}
?>
