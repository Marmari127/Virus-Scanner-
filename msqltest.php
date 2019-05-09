<?php

	require_once('login1.php');
	$conn = new mysqli($hn,$un,$pw, 'super_database');
	if($conn->connect_error) die($conn->connect_error);


	$query = "CREATE DATABASE test_database";
	$result = $conn->query($query);
	if(!$result) die ($conn->error);


	$query = "CREATE TABLE test (name varchar(32), middle_name varchar(32), last_name varchar(32))";
	$result = $conn->query($query);
	if(!$result) die ($conn->error);

	$fname = "Samantha";
	$mname = "Lucia";
	$lname = "Jaime";

	add_user($conn, $fname, $mname, $lname);

	function add_user($connection, $fn, $mn, $ln) 
	{
	    $query = "INSERT INTO test VALUES('$fn', '$mn', '$ln')";
	    $result = $connection->query($query);
	    if(!$result) die($conn->error);
	    echo $fn. " " .$mn. " " .$ln;
	}
?>