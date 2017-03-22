<?php

class loginDB {

private $logidb;

public function __construct() {
	$this->logindb = new mysqli('localhost', 'root', 'mjym4870', 'login' );
	if ($this->logindb->connect_error){
	die('Connect Error (' . $this->logindb->connect_errno . ')' . $this->logindb->connect_error);
	}
	
	echo 'Success... ' . $this->logindb->host_info . "\n";
}

public function getInfo($usern, $passw){
	$uname = $this->logindb->real_escape_string($usern);
	$pwd = $this->logindb->real_escape_string($passw);
	$userInfo = "Select * from users where screenname = '$uname' AND password = '$pwd'";
	$response = $this->logindb->query($userInfo);
	while ($row = $response->fetch_assoc())
	{
		echo "checking password for $usern".PHP_EOL;
		if ($row["password"] == $passw)
		{
			echo "passwords match for $usern".PHP_EOL;
			return 1;// password match
		}
		echo "passwords did not match for $usern".PHP_EOL;
	}
	return 0;//no users matched username
}

public function newUser($usern, $passw, $email){
	$uname = $this->logindb->real_escape_string($usern);
	$pwd = $this->logindb->real_escape_string($passw);
	$em = $this->logindb->real_escape_string($email);
	$newInfo = "Insert into users (screenname, password, email) values ('$uname', '$pwd', '$em')";
	$response = $this->logindb->query($newInfo);
	return true;
}

}

?>
