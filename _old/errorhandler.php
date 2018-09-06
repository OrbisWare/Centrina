<?php
namespace Centrina\Core\Classes;

class ErrorHandler {
	protected $errorList = array(
		0 => "Page does not exist.",
		1 => "Registration is disabled.",
		2 => "Couldn't validate email.",
		3 => "Invalid password configuration. (Contact support)",
		4 => "Email already exists.",
		5 => "Username is already taken.",
		6 => "Password or username is incorrect.",
		7 => "You have to wait until you can try logging in again.",
		8 => "Could not initiate a secure session (ini_set)",
		9 => "Failed to correct directory.",
		10 => "No file selected for upload.",
		11 => "Upload parameters invalid.",
		12 => "Wrong file type.",
		13 => "Failed to move uploaded file.",
		14 => "You have to be logged out to access this page.",
		15 => "You have to be logged in to access this page.",
		16 => "MySQL object isn't connected to via server.",
		17 => "Unable to get content. Page number is out of range.",
		18 => "There's already an API key with that name.",
		19 => "Something as gone wrong, please redo captcha.",
		20 => "That IP is reserved or already exist.",
		21 => "That IP is reserved and can't be added to your netmap.",
		22 => "That server already exists on that port.",
		40 => "Loader type not found.",
		1337 => "Resistance is futile, all your passwords are belong to us.",
	);

	protected function getError($id)
	{
		if( array_key_exists($id, $this->errorList) )
		{
			return $this->errorList[$id];
		}else{
			return false;
		}
	}

	protected function writeError($txt, $forceExit = false)
	{
		$returnArr = array(
			"status" => "error",
			"message" => $txt,
		);

		if($forceExit)
			exit();

		return $txt;
	}

	public static function error(...$args)
	{
		$numargs = count($args);
		if($numargs < 1)
			return $this->writeError( "Error ".join_zero(0).": No arguments for error. (Contact Support)", true );

		$id = $args[0];
		if($numargs == 1)
		{
			$err = $this->getError($id);
			if( isset($err) )
				return $this->writeError( "Error ".join_zero($id).": ".$err );
			
		}else{
			$_err = $this->getError($args[0]);
			$_arg_list = "Error ".join_zero($id).": ".$_err;

			array_unshift($args, $_arg_list);
			return $this->writeError( call_user_func_array("sprintf", $args) );
		}
	}

	public static function notice($msg)
	{
		$msg = htmlspecialchars($msg);
		return $msg;
	}
}
?>