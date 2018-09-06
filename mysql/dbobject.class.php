<?php
/**
 * Centrina PHP Framework
 *
 * Orginally written by Kevin "Tigerkev" H for project Aura, refined for Centrina.
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
namespace Centrina\System\MySQL;

class DBObject {
	protected $_stmt;

	public function __construct($stmt)
	{
		$this->_stmt = $stmt;
	}

	public function bind_param($a, &...$param)
	{
		array_unshift($param,$a);
		$rc = call_user_func_array(array($this->_stmt, 'bind_param'), $param);
		if(false===$rc)
		{
			trigger_error('bind_param() failed: '.htmlspecialchars($this->_stmt->error), E_USER_WARNING);
		}
		return $rc;
	}

	public function execute()
	{
		$rc = $this->_stmt->execute();
		if(false===$rc)
		{
			trigger_error('execute() failed: '.htmlspecialchars($this->_stmt->error), E_USER_WARNING);
		}
		return $rc;
	}

	public function store_result()
	{
		$rc = $this->_stmt->store_result();
		$this->num_rows = $this->_stmt->num_rows;
		return $rc;
	}

	public function bind_result(&...$args)
	{
		$rc = call_user_func_array(array($this->_stmt, 'bind_result'), $args);
		if(false===$rc)
		{
			trigger_error('bind_result() failed: '.htmlspecialchars($this->_stmt->error), E_USER_WARNING);
		}
		return $rc;
	}

	public function fetch_array()
	{
		return $this->_stmt->fetch_array();
	}

	public function fetch_row()
	{
		return $this->_stmt->fetch_row();
	}

	public function fetch_assoc()
	{
		return $this->_stmt->fetch_assoc();
	}

	public function fetch()
	{
		$this->num_rows = $this->_stmt->num_rows;
		$this->_stmt->fetch();
	}

	public function fetch_all()
	{
		$result = $this->_stmt->get_result();
		while($data = $result->fetch_assoc())
		{
			return $data;
		}
	}

	public function close()
	{
		$this->_stmt->close();
	}
}
?>
