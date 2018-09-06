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
 * @category  Modules
 */
namespace Centrina\System\Modules;

class Paginator {
	private $_conn;
	private $_limit;
	private $_query;
	private $_total;
	private $_page;
	private $_lastPage;

	public function __construct($conn, $query)
	{
		if($conn->isConnected())
		{
			$this->_conn = $conn;
			$this->_query = $query;
		}else{
			ErrorHandler::error(16);
			return;
		}
	}

	private function writeURL($class, $base_url = "", $page = null)
	{
		if($class == "disabled" || $class == "active")
			return "#";

		if(!$base_url == "")
			return $base_url."&pg=".$page;

		return false;
	}

	public function getData($limit = 10, $page = 1)
	{
		//we set variables
		$this->_limit = $limit;
		$this->_page = $page;

		//we attach the limit to query
		if($this->_limit == "all")
		{
			$query = $this->_query;
		}else{
			$query = $this->_query." LIMIT ".(($this->_page - 1) * $this->_limit).",$this->_limit";
		}

		//we run the mysqli query
		if($rs = $this->_conn->query($query))
		{
			while($row = $rs->fetch_assoc())
			{
				$results[] = $row;
			}
		}else{
			return false;
		}

		if(!isset($results))
			return false;

		$this->_total = count($results); //we have to set the total here.

		//we structure our returned array and return it
		$result = new stdClass();
		$result->page = $this->_page;
		$result->limit = $this->_limit;
		$result->total = $this->_total;
		$result->data = $results;

		return $result;
	}

	public function createLinks($links, $list_class, $base_url = "")
	{
		if($this->_limit == "all")
			return "";

		$last = ceil($this->_total / $this->_limit);
		echo "<script>console.log('".$this->_total."');</script>";

		$start = ( ($this->_page - $links) > 0 ) ? $this->_page - $links : 1;
		echo "<script>console.log('".$start."');</script>";
		$end = ( ($this->_page + $links) < $last ) ? $this->_page + $links : $last;
		echo "<script>console.log('".$end."');</script>";

		$html = "<ul class='".$list_class."'>";

		$class = ($this->_page == 1) ? "disabled" : "";
		$html .= "<li class='".$class."'><a href='".$this->writeURL($class, $base_url, $this->_page - 1)."'>&laquo;</a></li>";

		if($start > 1)
		{
			echo "<script>console.log('if check');</script>";
			$html .= "<li><a href='".$this->writeURL($class, $base_url, 1)."'>1</a></li>";
			$html .= "<li class='disabled'><span>...</span></li>";
		}

		for($i = $start; $i <= $end; $i++)
		{
			$class = ($this->_page == $i) ? "active" : "";
			$html .= "<li class='".$class."'><a href='".$this->writeURL($class, $base_url, $i)."'>".$i."</a></li>";
		}

		if($end < $last)
		{
			$html .= '<li class="disabled"><span>...</span></li>';
			$html .= "<li><a href='".$this->writeURL($class, $base_url, $last)."'>'".$last."'</a></li>";
		}

		$class = ($this->_page == $last) ? "disabled" : "";
		$html .= "<li class='".$class."'><a href='".$this->writeURL($class, $base_url, $this->_page + 1)."'>&raquo;</a></li>";

		$html .= "</ul>";

		return $html;
	}
}
?>
