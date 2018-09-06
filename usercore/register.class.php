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

/*
config shit:
	hash_algorithm
	serial_length
*/
namespace Centrina\System\UserCore;
use Centrina\System\Libs\Vivian as Vivian;
use Centrina\System\Libs\Utils as Utils;
use Centrina\System\Libs\PHPMailer as PHPMailer;
use Centrina\System\Libs\Murmur as Murmur;
use Centrina\System\Core\Hooks as Hooks;
use Centrina\System\Modules\Token as Token;

class Register extends User {
	public function __construct($conn)
	{
		parent::__construct($conn);
	}

	private function checkUserName($username)
	{
		$stmt = $this->_conn->prepare("SELECT `id` FROM ct_users WHERE `username` = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;

		if($rows > 0)
		{
			return true;
		}
		return false;
	}

	private function checkEmail($email) //we want to prevent alts, so we only allow one account per email
	{
		$stmt = $this->_conn->prepare("SELECT `id` FROM ct_users WHERE `email` = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;

		if($rows > 0)
		{
			return true;
		}
		return false;
	}

	private function checkCaptcha()
	{
		if($this->config["check_captcha"] === TRUE)
		{
			$key = $this->config["captcha_key"];
			$ip = $_SERVER["REMOTE_ADDR"];
			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$key."&response=".$captcha."&remoteip=".$ip);
			$responseKeys = json_decode($response, true);

			if(!$captcha)
				return false;

			if(intval($responseKeys["success"]) != 1)
			{
				return false;
			}else{
				return true;
			}
		}

		return true;
	}

	private function sendVerifyEmail($email, $key)
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

		$mail->Subject = sprintf($this->__lang('email_activation_subject'), $this->config["site_name"]);
		$mail->Body = sprintf($this->__lang('email_activation_body'), $this->config["site_url"], $this->config["site_activation_page"], $key);
		$mail->AltBody = sprintf($this->__lang('email_activation_altbody'), $this->config["site_url"], $this->config["site_activation_page"], $key);

		if(!$mail->send())
		{
			throw new \Exception($mail->ErrorInfo);
		}catch(\Exception $e){
			trigger_error($mail->ErrorInfo, E_USER_ERROR);
			return false;
		}
	}

	private function doVerify()
	{
		$tObj = new Token($this->_conn);
		$tObj->create(Utils::getClientIP());
		$token = $tObj->get();

		$this->sendVerifyEmail($email, $token);
	}

	public function register($username, $email, $password)
	{
		if($this->config["register_disabled"] === TRUE)
			return false;

		if(!$this->checkCaptcha())
		{
			Hooks::call("RegisterFailure");
			$this->error("19:captcha_incorrect");
			return false;
		}

		if(empty($email))
		{
			Hooks::call("RegisterFailure");
			$this->error("02:email_empty");
			return false;
		}

		if(empty($password))
		{
			Hooks::call("RegisterFailure");
			$this->error("03:password_empty");
			return false;
		}

		if($this->checkUserName($username))
		{
			Hooks::call("RegisterFailure");
			$this->error("05:username_taken");
			return false;
		}

		if($this->checkEmail($email))
		{
			Hooks::call("RegisterFailure");
			$this->error("04:email_taken");
			return false;
		}

		$time = time();
		$user_ip = ip2long(Utils::getClientIP());
		$user_browser = Murmur::hash3($_SERVER["HTTP_USER_AGENT"]);
		$verified = false;

		if($this->config["account_verification"] === TRUE)
		{
			$this->doVerify();
		}else{
			$verified = true;
		}

		$random_salt = UHelper::generateSalt();
		$password = Vivian::smartHash($password . $random_salt);

		$stmt = $this->_conn->prepare("INSERT INTO ct_users(`username`, `email`, `password`, `salt`, `time`, `ip`, `verified`, `browser`) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssiiis", $username, $email, $password, $random_salt, $time, $user_ip, $verified, $user_browser);
		$stmt->execute();
		$stmt->close();

		Hooks::call("RegisterSuccess");
		return true;
	}
}
?>
