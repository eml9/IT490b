<?php

class movieDB {

private $moviedb;

public function __construct() {
	$this->moviedb = new mysqli('localhost', 'root', 'mjym4870', 'movie' );
	if ($this->moviedb->connect_error){
	die('Connect Error (' . $this->moviedb->connect_errno . ')' . $this->moviedb->connect_error);
	}
	
	echo 'Success... ' . $this->moviedb->host_info . "\n";
}

public function newTable($uname,$pwd,$em){
        
        //$newTable = "Select * from user where username = '$uname'";
        $create = "Create table '$uname' (username VARCHAR(20), password VARCHAR(20),email VARCHAR(20) )";
        $insert = "Insert into '$uname' (username, password, email) values ('$uname','$pwd','$em')";
        //$response = $this->moviedb->query($newTable);
        $response1 = $this->moviedb->query($create);
        $response2 = $this->moviedb->query($insert);
        
}
}
?>