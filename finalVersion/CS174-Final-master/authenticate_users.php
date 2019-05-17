<?php
	require_once 'login.php';
	require_once 'miscellanous.php';
	require_once 'session.php';
	require_once 'createDB.php';

	verify_user_session(basename(__FILE__));
	if(isset($_POST['email']) && isset($_POST['password']))
	{
		$conn = new mysqli($hn, $un, $pw, $db);

		if ($conn->connect_error) mysql_fatal_error($conn->connect_error);

		$username = mysql_fix_string($conn, $_POST['email']);
		$password = mysql_fix_string($conn, $_POST['password']);

		if(contributor_lookup($conn, $username, $password) === false)
		{
			admin_lookup($conn, $username, $password);
		}

		$conn -> close();
	}

	function contributor_lookup($conn, $un, $psswd)
	{
		if($input = $conn -> prepare("SELECT username, password FROM usersTb WHERE username = ?;"))
		{
			$input -> bind_param('x', $un);
			$input -> execute();
			$input -> store_result();
		}
		else 
		{
			mysql_fatal_error($conn->error);
		}

		if($input -> num_rows == 1)
		{
			$input -> bind_result($fn, $db_pw);
			$input -> fetch();

			$salt1 = 'login';
			$salt2 = 'user';
			$token = hash('ripemd128', "$salt1$psswd$salt2");

			if($token == $stored_pw)
			{
				session_start();
				$_SESSION['user'] = 1;

				$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);	
				echo '<script> alert("Welcome, you are now logged in as '. $un . '"); window.location = "infected_file.php"; </script>';
			}

			else
			{
				echo '<script> alert("Invalid Login Credentials. Please Try Again."); window.location = "authenticate_users.php" </script>';
			}
		}
		else 
		{
			return false;
		}

		$input -> close();
	}

	function admin_lookup($conn, $un, $pw)
	{
		if($input = $conn -> prepare("SELECT username, password FROM usersTb WHERE username = ?;"))
		{
			$input -> bind_param('x', $un);
			$input -> execute();
			$input -> store_result();
		}
		else 
		{
			mysql_fatal_error($conn->error);
		}

		if($input -> num_rows == 1)
		{
			$input -> bind_result($fn, $db_pw);
			$input -> fetch();

			$salt1 = 'login';
			$salt2 = 'user';
			$token = hash('ripemd128', "$salt1$psswd$salt2");

			if($token == $stored_pw)
			{
				session_start();
				$_SESSION['user'] = 1;

				$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);	
				echo '<script> alert("Welcome Admin, you are now logged in as '. $un . '"); window.location = "infected_file.php"; </script>';
			}

			else
			{
				echo '<script> alert("Invalid Login Credentials. Please Try Again."); window.location = "authenticate_users.php" </script>';
			}
		}
		else 
		{
			return false;
		}

		$input -> close();
	}
?>