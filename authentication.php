<?php 
	//The DB credentials 
	require_once(login.php);
	$conn = new mysqli($hn, $un, $pw, "master_database");
	if($conn->connect_error) die($conn->connect_error);

	//authentication for admin and contributor
	if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) 
	{
		$uname_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
		$psswd_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);

		$query = "SELECT FROM usersTb WHERE username = '$uname_temp' ";
		$result = $conn ->query($query);

		if(!result)
		{
			die($conn->error)
		}
		else if($result->num_rows)
		{
			$row = $result ->fetch_array(MYSQLI_NUM);
			$result->close();

			$salt1 = "Hacking";
			$salt2 = "Duo";
			$token = hash('ripemd128', "$salt1$psswd_temp$salt2");

			if($token == $row[3])
			{
				session_start();
				$_SESSION['first_name'] = $row[0];
				$_SESSION['last_name'] = $row[1];
				$_SESSION['username'] = $row[2];
				$_SESSION['passwd'] = $row[3];
				$_SESSION['isAdmin'] = $row[4];

				if($row[4] == 1)
				{
					echo "Welcome $row[1]. you are succesfully logged as Admin!";
				}
				else
				{
					echo "Welcome $row[1]. you are succesfully logged in as Contributor!";
				}

				die("<p><a href = continue.php> Click Here to Continue</a></p>");
			}
			else 
			{
				die("Invalid username and/or password");
			}
		}
		else 
		{
			die("Invalid username and/or password");
		}
	}
	else 
	{
		header('WWW-Authenticate: Basic realm="Restricted Section"');
		header('HTTP/1.0 401 Unauthorized');
		die("Please enter your username and password");
	}

	$conn -> close();

	//sanitize
	function mysql_entities_fix_string($connection, $s) 
	{
   		 return htmlentities(mysql_fix_string($connection, $s));
	}

	//sanitize
	function mysql_fix_string($connection, $s) 
	{
    	if(get_magic_quotes_gpc())
    	{
    		$s = stripslashes($s);
    	}
    	return $connection->real_escape_string($s);
	}
?>