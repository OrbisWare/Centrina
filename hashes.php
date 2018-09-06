<?php
/*================
	Usable Hashes
		List of hashes that are supported by the system with a correlating ID. Allows us to switch to a different hash on the fly per se.
		The system will automaticly change a users password hash when they login with a different hash.
================*/

return array(
	1 => "sha512",
	2 => "whirlpool",
	3 => "ripemd320",
	4 => "haval256,5",
	5 => "gost-crypto"
);
?>