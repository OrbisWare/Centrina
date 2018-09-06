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
namespace Centrina\System\UserCore;
use Centrina\System\Libs\Vivian as Vivian;
use Centrina\System\Libs\Utils as Utils;
use Centrina\System\Libs\Murmur as Murmur;
use Centrina\System\Modules\Token as Token;
use Centrina\System\Core\Hooks as Hooks;

class Login extends User {
	public function __construct($conn)
	{
		parent::__construct($conn);
	}

	private function checkTimeout($userid)
	{
		$now = time();
		$valid_attempts = $now - (2 * 60 * 60);
		$max_attempts = $this->config["login_max_attempts"];

		$stmt = $this->_conn->prepare("SELECT `time` FROM ct_loginattempts WHERE `user_id` = ? AND `time` > ?");
		$stmt->bind_param("ii", $userid, $valid_attempts);
		$stmt->execute();
		$data = $stmt->fetch();

		$time = $data["time"];
		if($time >= $max_attempts)
			return true;
		else
			return false;
	}

	private function checkeAuth($userid)
	{
		$curIP = ip2long( Utils::getClientIP() );
		$curBrowser = Murmur::hash3($_SERVER["HTTP_USER_AGENT"]);

		$stmt = $this->_conn->prepare("SELECT `browser`, `ip` FROM ct_users WHERE `user_id` = ?");
		$stmt->bind_param("i", $userid);
		$stmt->execute();
		$data = $stmt->fetch_all();

		$dbBrowser = $data["browser"];
		$dbIP = $data["ip"];

		//We do hash_equals to avoid any "time" attacks, it is safer this way.
		$browserCheck = hash_equals($curBrowser, $dbBrowser);
		$ipCheck = hash_equals($curIP, $dbIP);

		if( $browserCheck && $ipCheck === FALSE )
			return false;
	}

	private function addLoginAttempt($userid)
	{
		$now = time();
		$ip = ip2long( Utils::getClientIP() );

		$stmt = $this->_conn->prepare("INSERT INTO ct_loginattempts(`user_id`, `time`, `user_ip`) VALUES(?, ?, ?)");
		$stmt->bind_param("iii", $userid, $now, $ip);
		$stmt->execute();
		$stmt->close();
	}

	public function createSecureCookie($username, $password)
	{
		$stmt = $this->_conn->prepare("SELECT `salt` FROM ct_users WHERE `username` = ? LIMIT 1");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$data = $stmt->fetch();

		$salt = $data["salt"];

		$password = Vivian::smartHash($password . $salt);

		setcookie( $this->config["login_cookie"], $username . "," . $password, time() + (10 * 365 * 24 * 60 * 60), "/" );
	}

	private function sendAuthEmail($email, $key)
	{
		$mail = new PHPMailer();

		// Check configuration for SMTP parameters
		try {
			$mail->SMTPDebug = $this->config["smtp_debug"];
			$mail->isSMTP();

			$mail->Host = $this->config["smtp_host"];
			$mail->SMTPAuth = $this->config["smtp_auth"];

			if($this->config["smtp_auth"])
			{
		    $mail->Username = $this->config["smtp_username"];
				$mail->Password = $this->config["smtp_password"];
			}

			// set SMTPSecure (tls|ssl)
			$mail->SMTPSecure = $this->config["smtp_security"];
			$mail->Port = $this->config["smtp_port"];
		}

		//Recipients
		$mail->setFrom($this->config["site_email"], $this->config["site_name"]);
		$mail->addAddress($email);

		$mail->CharSet = $this->config["mail_charset"];

		//Content
		$mail->isHTML(true);

		$mail->Subject = sprintf($this->__lang('email_auth_subject'), $this->config["site_name"]);
		$mail->Body = sprintf($this->__lang('email_auth_body'), $this->config["site_url"], $this->config["site_auth_page"], $key);
		$mail->AltBody = sprintf($this->__lang('email_auth_altbody'), $this->config["site_url"], $this->config["site_auth_page"], $key);

		if(!$mail->send())
		{
			throw new \Exception($mail->ErrorInfo);
		}catch(\Exception $e){
			trigger_error($mail->ErrorInfo, E_USER_ERROR);
			return false;
		}
	}

	private function sendPasswordEmail($email, $username, $password);
	{
		$mail = new PHPMailer();

		// Check configuration for SMTP parameters
		try {
			$mail->SMTPDebug = $this->config["smtp_debug"];
			$mail->isSMTP();

			$mail->Host = $this->config["smtp_host"];
			$mail->SMTPAuth = $this->config["smtp_auth"];

			if($this->config["smtp_auth"])
			{
		    $mail->Username = $this->config["smtp_username"];
				$mail->Password = $this->config["smtp_password"];
			}

			// set SMTPSecure (tls|ssl)
			$mail->SMTPSecure = $this->config["smtp_security"];
			$mail->Port = $this->config["smtp_port"];
		}

		//Recipients
		$mail->setFrom($this->config["site_email"], $this->config["site_name"]);
		$mail->addAddress($email);

		$mail->CharSet = $this->config["mail_charset"];

		//Content
		$mail->isHTML(true);

		$mail->Subject = sprintf($this->__lang('email_forgotpass_subject'), $this->config["site_name"]);
		$mail->Body = sprintf($this->__lang('email_forgotpass_body'), $username, $password);
		$mail->AltBody = sprintf($this->__lang('email_forgotpass_altbody'), $username, $password);

		if(!$mail->send())
		{
			throw new \Exception($mail->ErrorInfo);
		}catch(\Exception $e){
			trigger_error($mail->ErrorInfo, E_USER_ERROR);
			return false;
		}
	}

	private function doeAuth($email)
	{
		$tObj = new Token($this->_conn);
		$tObj->create(Util::getClientIP());
		$token = $tObj->get();

		$this->sendAuthEmail($email, $token);
		//Send email to perform the check to see if this is actually the owner of via account.
	}

	//just incase the user forgets their username too, we'll do both at the same time because we're so clever.
	public function requestPasswordReset($email)
	{
		$stmt = $this->_conn->prepare("SELECT `username`, `id` FROM ct_users WHERE `email` = ? LIMIT 1");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$data = $stmt->fetch_all();

		$username = $data["username"];
		$userid = $data["id"];
		if(!is_null($username))
		{
			$len = $this->config["random_password_length"];
			$password = Utils::generateSecureStr($len);
			$this->forceChangePassword($userid, $password);
			$this->sendPasswordEmail($email, $username, $password);
		}
	}

	public function login($username, $password)
	{
		if($this->config["login_disabled"] === TRUE)
		{
			$this->error("16:login_dead");
			return false;
		}

		$stmt = $this->_conn->prepare("SELECT `id`, `username`, `email`, `password`, `salt`, `browser`, `ip`, `verified`, `eauth` FROM ct_users WHERE `username` = ? LIMIT 1");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$data = $stmt->fetch_all();

		$password = Vivian::smartHash($password . $data["salt"]);

		if($data)
		{
			$userid = $data["id"];
			$userip = $data["ip"];
			$email = $data["email"];
			$verified = boolval($data["verified"]);
			$eAuth = boolval($data["eauth"]); //This is for 2 factor authenication.
			$hashComp = Vivian::hashCompare($password, $data["password"]);

			if($this->config["login_ip_lock"] === TRUE)
			{
				if($userip != Utils::getClientIP())
				{
					Hooks::call("LoginIPFail"); //Let the developers control what happens if the user connects with a different IP. Used for high security stuffs.
				}
			}

			if($verified === FALSE)
			{
				$this->error("08:verification_required");
				return false;
			}

			if($this->checkTimeout($userid) === TRUE)
			{
				Hooks::call("LoginTimeout");
				$this->error("07:login_wait");
				return false;
			}

			//This is my version of 2 factor authenication, it will send an email to the user to see if they're the one trying to login. I do this because I hate phone authenicators.
			if($eAuth === TRUE)
			{
				if(Hooks::call("Login2Auth"))
				{
					Hooks::call("Login2Auth"); //Allow developers to do their own 2 factor authenication code, if not then it will default.
				}

				if($this->checkeAuth() === FALSE)
				{
					$this->doeAuth($email);
				}
			}

			//First we want to check the hash version too see if it's up to date.
			if($hashComp[0] === FALSE)
			{
				//UHelper::forcePasswordChange($userid, TRUE);
				$this->requestPasswordReset($email);
			}

			//Then, we want to actually check the password it self to see if it matches...
			if($hashComp[1] === TRUE)
			{
				Hooks::call("LoginSuccess");
				$userid = preg_replace("/[^0-9]+/", "", $userid);
				$user_browser = $_SERVER["HTTP_USER_AGENT"];

				$_SESSION["user_id"] = $userid;
				$_SESSION["username"] = $username;
				$_SESSION["email"];
				$_SESSION["user_string"] = Vivian::smartHash($password . $user_browser);
				$this->updateLastOnline();

				return true;
			}

			Hooks::call("LoginIncorrect");
			$this->addLoginAttempt($userid);
			$this->error("06:login_incorrect");
			return false;
		}

		Hooks::call("LoginIncorrect");
		$this->error("06:login_incorrect");
		return false;
	}
}
?>
