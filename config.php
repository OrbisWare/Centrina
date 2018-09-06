<?php
/*================
	User System Config
================*/
return array(
	"mysql_host" => "127.0.0.1",
	"mysql_username" => "root",
	"mysql_password" => "",
	"mysql_database" => "centrina",

	"site_name" => "Centrina",
	"site_url" => "http://www.orbisware.com/",
	"site_email" => "noreply@orbisware.com",
	"site_activation_page" => "activateuser/",
	"site_auth_page" => "auth/",

	"secure_session" => FALSE, //If we want our session secure or not. Used with HTTPS protocol instead of normal HTTP.

	"hash_version" => 1, //What hashing version should we use. Look at hashes.php for reference.

	"smtp_debug" => false,
	"smtp_host" => 127.0.0.1,
	"smtp_auth" => true,
	"smtp_security" => "ssl",
	"smtp_username" => "",
	"smtp_password" => "",
	"mail_charset" => "utf8",

	"account_verification" => FALSE,
	"register_disabled" => FALSE,
	"random_password_length" => 16,

	"check_captcha" => FALSE,
	"captcha_key" => "", //The private key for Google Captcha

	"login_disabled" => FALSE, //Disable users rom being able to login to your site.
	"login_cookie" => "ct_loginremain", //We set the cookie in multiple places
	"login_max_attempts" => 3, //The max amount of login attempts allowed before the vortex from our time machine eats you.
	"login_ip_lock" => FALSE,

	"token_length" => 64, //the token length
	"token_time" => 86400, //24 hours

	"invite_length" => 32, //the invite code length
	"invite_time" => 0, //the amount of time in seconds the invite code should last. 0 for infinite

	/*"log_type" => "mysql", //mysql for mysql storage of logs. file for file storing.
	"log_path" => "",
	"datetime_format" => "",
	"date_format" => "",*/
);
?>
