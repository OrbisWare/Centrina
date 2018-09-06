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
namespace Centrina\System\UserCore\Gatekeepr;
use Centrina\System\Core\CT_Core as CT_Core;

class Gatekeepr {
  private $_conn;
  private $_userid;
  private $permissios = array();

  public function __construct($conn, $userid = null)
	{
    if($conn->isConnected())
    {
      if(!is_null($userid))
        $this->_userid = $userid;

      $this->_conn = $conn;
    }else{
      throw new Exception("Gatekeepr: Unable to find via connection to via database.");
			return;
    }
	}

  public function loadPermissions()
  {
    $userid = $this->_userid;
    $stmt = $this->_conn->prepare(
      "SELECT t1.perm_id FROM ct_roleperms as t1 JOIN ct_userroles as t2
      WHERE t1.role_id = t2.role_id AND t1.user_id = ?"
    );
		$stmt->bind_param("i", $userid);
		$stmt->execute();
		$data = $stmt->fetch();

    $this->permissions[$userid] = $data;
  }

  public function checkUserPerm($permid)
  {
    if(empty($this->permissions))
    {
      trigger_error("Object Error Gatekeepr: Unable to find permissions from array.", E_USER_NOTICE);
      return FALSE;
    }

    if(isset($this->permissions[$this->_userid][$permid]))
      return TRUE;
    else
      return FALSE;
  }

  public function clearArray()
  {
    if(!empty($this->permissions))
      $this->permissions = array();
  }

  public function resetArray()
  {
    unset($this->permissions);
    $this->loadPermissions();
  }

  public function addRoleToUser($roleid)
  {
    $userid = $this->_userid;

    $stmt = $this->_conn->prepare("INSERT INTO ct_userroles(`user_id`, `role_id`) VALUES(?, ?)");
    $stmt->bind_param("is", $userid, $roleid);
    $stmt->execute();
    $stmt->close();
  }

  public function removeRoleFromUser($roleid)
  {
    $userid = $this->_userid;

    $stmt = $this->_conn->prepare("DELETE FROM ct_userroles WHERE `user_id` = ? AND `role_id` = ?");
    $stmt->bind_param("is", $userid, $roleid);
    $stmt->execute();
    $stmt->close();
  }

  private function checkRoleId($roleid)
  {
    $stmt = $this->_conn->prepare("SELECT `id` FROM ct_permissions WHERE `id` = ?");
		$stmt->bind_param("s", $roleid);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;

		if($rows > 0)
		{
			return true;
		}
		return false;
  }

  public function createRole($id, $desc)
  {
    if(!$this->checkRoleId($id))
      return false;

    $stmt = $this->_conn->prepare("INSERT INTO ct_roles(`id`, `rdesc`) VALUES(?, ?)");
    $stmt->bind_param("ss", $id, $desc);
    $stmt->execute();
    $stmt->close();
  }

  public function deleteRole($roleid)
  {
    //we have to do a cascade delete here to remove record of the role from all tables.
  }

  private function checkPermId($permid)
  {
    $stmt = $this->_conn->prepare("SELECT `id` FROM ct_permissions WHERE `id` = ?");
		$stmt->bind_param("s", $permid);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;

		if($rows > 0)
		{
			return true;
		}
		return false;
  }

  public function createPermission($id, $desc)
  {
    if(!$this->checkPermId($id))
      return false;

    $stmt = $this->_conn->prepare("INSERT INTO ct_permissions(`id`, `pdesc`) VALUES(?, ?)");
    $stmt->bind_param("ss", $id, $desc);
    $stmt->execute();
    $stmt->close();
  }

  public function removePermission($permid)
  {
    //we have to do a cascade delete here to remove record of the permission from all tables.
  }

  public function addPermToRole($permid, $roleid)
  {
    $stmt = $this->_conn->prepare("INSERT INTO ct_roleperms(`role_id`, `perm_id`) VALUES(?, ?)");
    $stmt->bind_param("ss", $permid, $roleid);
    $stmt->execute();
    $stmt->close();
  }

  public function removePermFromRole($roleid, $permid)
  {
    $stmt = $this->_conn->prepare("DELETE FROM ct_roleperms WHERE `perm_id` = ? AND `role_id` = ?");
    $stmt->bind_param("ss", $permid, $roleid);
    $stmt->execute();
    $stmt->close();
  }
}
?>
