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
 * @category  Core
 */
use Centrina\System\Core\CT_Core as CT_Core;

//You can redefine these as needed for whatever, just be careful while doing so.
define("CT_APPPATH", $_SERVER["DOCUMENT_ROOT"]."/system"); //This is the file path to Centrina.
define("CT_ROOT", $_SERVER["DOCUMENT_ROOT"]); //This is the root for your website.
define("CT_VERSION", "1.0.0dev"); //This is just the version of Centrina, not really needed for anything important.

if( version_compare(PHP_VERSION, "5.6.0", "<=") )
	die("Centrina: Running PHP version ".PHP_VERSION.". You must be running 5.6.0 or greater.\n");

/**
* This is the default configuaration for CSRF Magic by Edward Z. Yang
*
* This is a function that gets called if a csrf check fails. csrf-magic will
* then exit afterwards.
*/
function my_csrf_callback() {
	echo "You're doing bad things young man!";
}

function csrf_startup()
{
	// While csrf-magic has a handy little heuristic for determining whether
	// or not the content in the buffer is HTML or not, you should really
	// give it a nudge and turn rewriting *off* when the content is
	// not HTML. Implementation details will vary.
	if (isset($_POST['ajax'])) csrf_conf('rewrite', false);

	// This is a secret value that must be set in order to enable username
	// and IP based checks. Don't show this to anyone. A secret id will
	// automatically be generated for you if the directory csrf-magic.php
	// is placed in is writable.
	csrf_conf('secret', 'ABCDEFG123456');

	// This enables JavaScript rewriting and will ensure your AJAX calls
	// don't stop working.
	csrf_conf('rewrite-js', 'js/csrf-magic.js');

	// This makes csrf-magic call my_csrf_callback() before exiting when
	// there is a bad csrf token. This lets me customize the error page.
	csrf_conf('callback', 'my_csrf_callback');

	// While this is enabled by default to boost backwards compatibility,
	// for security purposes it should ideally be off. Some users can be
	// NATted or have dialup addresses which rotate frequently. Cookies
	// are much more reliable.
	csrf_conf('allow-ip', false);
}

require_once("cstf-magic.php"); //This will automaticly do CSRF protection, saving developers some time. Some day I'll get around to writing my own.
require("core.class.php");

CT_Core::initSystem();
?>
