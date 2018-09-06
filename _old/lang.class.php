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

class Lang {
  protected $langList;

  public function __construct()
  {
    $this->langList = array();
  }

  public function register(array $langList)
  {
    array_filter($this->langList); //we want to remove old values from the array.

    foreach($langList as $key => $val)
    {
      if($this->langList[$key])
      {
        throw new \Exception("Unable to register language list:".$key." is already registered.");
        return;
      }

      if(!is_string($val))
      {
        throw new \Exception("Unexpected variable type, value must be a string. ".$key);
        return;
      }
    }

    $this->langList = array_merge($langList, $this->langList);
  }

  public function lang($id)
  {
    $langList = $this->langList;
    if( !$langList[$id] )
    {
      throw new \Exception("Unexpected ID:".$key);
      return;
    }
    return $langList[$key];
  }
}
?>
