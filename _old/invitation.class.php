<?php
namespace Centrina\System\Modules;

/*
	
*/

define("INVITE_LENGTH", 32)

class Invitation {
	private $_inviteLen = INVITE_LENGTH;
	protected $_inviteCode;
	protected $_conn;

	public function __construct($conn)
	{
		if($conn->isConnected())
		{
			$this->_conn = $conn;
		}else{
			return;
		}
	}

	public function InviteLength($len)
	{
		$this->_inviteLen = $len;
	}

	public function GenerateCode()
	{
		$char = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
				"R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i",
				"j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		$num = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

		$key = "";

		for($i = 0; $i < $this->_inviteLen; $i++)
		{
			$rl = "";
			if(rand()%100 <= 50)
				$rl = $char[rand(0, 51)];
			else
				$rl = $num[rand(0, 10)];

			$key = (string)$key.(string)$rl;
		}

		if( $this->CheckInvite($key) )
		{
			$this->_inviteCode = $key;
		}else{
			$this->GenerateCode();
		}
	}

	public function CheckInvite($code)
	{
		$code = htmlspecialchars($code);

		$stmt = $this->_conn->query("SELECT `sh_taken` FROM invites WHERE sh_code = '$code' AND sh_taken = 0 LIMIT 1");
		$row = $stmt->fetch_array();

		$claim = $row["sh_taken"];

		if($claim == 0)
		{
			return true; //is available
		}else{
			return false; //not available
		}
	}

	public function CreateInvite($userid)
	{
		$code = $this->_inviteCode;
		$time = time();

		$stmt = $this->_conn->prepare("INSERT INTO invites(sh_userid, sh_code, sh_time, sh_claim, sh_taken) values(?, ?, ?, ?, ?)");
		if($stmt)
		{
			$stmt->bindparam("isii", $userid, $code, $time, 0, 0);
			$stmt->execute();
			$stmt->close();
		}
	}

	public function GetInvite()
	{
		return $this->_inviteCode;
	}
}
?>
