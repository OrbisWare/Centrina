<?php
namespace Centrina\System\Core;

class Utils {
	public static function getUserID($username) //safest bet is username since they should always be unique ...I hope
	{
		global $mysqli;
		$id = 0;

		$stmt = $mysqli->query("SELECT rs_id FROM users WHERE rs_username='$username'");
		if($stmt)
		{
			$row = $stmt->fetch_array();
			$id = $row["rs_id"];
		}

		$stmt->close();
		return $id;
	}

	public static function count_users()
	{
		global $mysqli;
		if( $stmt = $mysqli->query("SELECT rs_id FROM `users`") )
		{
			$row = $stmt->fetch_row();
			return count($row[0]);
		}else{
			return 0;
		}

		$stmt->close();
	}

	public static function count_premiumUsers()
	{
		global $mysqli;
		if( $stmt = $mysqli->query("SELECT rs_id FROM `users` WHERE `rs_premium` = 1") )
		{
			$row = $stmt->fetch_row();
			return count($row[0]);
		}else{
			return 0;
		}

		$stmt->close();
	}

	//modified from the api key genration function.
	public static function generatePassword($len)
	{
		$char = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
				"R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i",
				"j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		$num = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
		$sym = array("!", "@", "#", "$", "%", "^", "&", "*", "-");

		$key = "";

		for($i = 0; $i < $len; $i++)
		{
			$rl = "";
			if(rand()%100 <= 50)
			{
				$rl = $char[rand(0, 51)];
			}else{
				$rl = $num[rand(0, 10)];
			}

			$key = (string)$key.(string)$rl;
		}

		$this->_curToken = $key;
		return $key;
	}

	public static function esc_url($url)
	{
		if ("" == $url)
		{
			return $url;
		}

		$url = preg_replace("|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i", "", $url);

		$strip = array("%0d", "%0a", "%0D", "%0A");
		$url = (string) $url;

		$count = 1;

		while ($count)
		{
			$url = str_replace($strip, "", $url, $count);
		}

		$url = str_replace(";//", "://", $url);
		$url = htmlentities($url);
		$url = str_replace("&amp;", "&#038;", $url);
		$url = str_replace("'", "&#039;", $url);

		if ($url[0] !== "/")
		{
			return "";
		}else{
			return $url;
		}
	}

	public static function generateRandomString($len = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $len; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

if(isset($_SESSION["user_id"]))
{
	$GLOBAL["uid"] = $_SESSION["user_id"]; //slower but nicer looking.
}
?>
