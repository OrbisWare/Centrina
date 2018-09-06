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
use Centrina\System\Libs\Utils as Utils;

class Language {
  protected $dictionary = array();
  protected $availableLangs = array();

  public function __construct()
  {
    $path = CT_APPPATH."/langs/";
    $langs = array_diff(scandir($path), array(".", ".."));

    for($i=0; sizeof($langs); $i++)
    {
      $oldName = $langs[$i];
      $newName = substr($oldName, 0, strrpos($oldName, "."));
      $langs[$i] = $newName;
    }

    $this->availableLangs = $langs;
  }

	public function translate($key)
	{
		$file = CT_APPPATH."/langs/".$file;
		$lang = array();
		if(is_readable($file) === TRUE)
		{
			return (array) call_user_func(function() use($file) {
				$lang include_once($file);
			});
		}else{
			trigger_error("Core: Unable to include langauge file: ".$file, E_USER_NOTICE);
		}
	}
}
?>
