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
 * @category  Middleware
 */

//they said I couldn't make my security anymore over the top, well here is more over the top ya checky cunts.
namespace Centrina\System\Middleware;

class Vidia{
  //construct
  protected $identifier = "";
  protected $hash = "";
  protected $encode = FALSE;
  protected $iplock = FALSE;
  protected $keyLen = 12;

  //returns
  protected $_salt;

  //storage
  protected $identifiers = array();

  public function __construct($id, $hash)
  {
    //quick access for the class
    $this->identifier = $id;
    $this->hash = $hash;

    $this->identifiers[$id] = $hash;
  }

  public function config($keyLen, $encode, $iplock)
  {
    $this->keyLen = $keyLen;
    $this->encode = $encode;
    $this->iplock = $iplock;
  }

  protected function generateKey()
  {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"; //the list of possible chars
    $key = ""; //our result

    $charsLen = strlen($chars); //easy access

    for($i = 0; $i < $this->keyLen; $i++)
    {
      $key .= $chars[mt_rand(0, $charsLen)];
    }

    return $key;
  }

  protected function generateID()
  {
    return microtime() . uniqid( mt_rand( 1, mt_getrandmax() ), true );
  }

  protected function userIP()
  {
    return ip2long( Centrina\System\Libs\Utils::getClientIP() );
  }

  public function runHash($str);
  {
    $hashStr;

    $key = $this->generateKey(); //random key
    $id = $this->generateID(); //random id
    $ip = $this->userIP();
    $salt = hash($this->hash, $id); //generate salt

    $this->_salt = $salt;

    $hashStr = $this->identifier; //add the identifier ex: $CT$
    $hashStr .= $key . "/"; //add the id ex: $CT$AJHsfJH56BBs13/
    $hashStr .= $str; //add the string ex: $CT$AJHsfJH56BBs13/Password123
    $hashStr .= $salt; //add the salt ex: $CT$AJHsfJH56BBs13/Password123ffgasdasd54ds6sdsdfsf6
    if( $this->iplock == TRUE )
    {
      $hashStr .= $ip; //add the long ip ex: $CT$AJHsfJH56BBs13/Password123ffgasdasd54ds6sdsdfsf63221234342
    }

    return hash($this->hash, $hashStr);
  }
}
?>
