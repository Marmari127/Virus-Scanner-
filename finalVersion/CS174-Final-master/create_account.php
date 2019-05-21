<style type="text/css">
</style>
<html>
	<title>Create Account</title>
	<link rel="stylesheet" type="text/css" href="index_css.css">
	<body>
		<form method="post">
	    <input name="firstName" placeholder="First Name" type="text" maxlength="128" required />
	    <input name="lastName"  placeholder="Last Name"  type="text" maxlength="128" required />
			<input name="email"     placeholder="Email"      type="email"    maxlength="128" required/>
			<input name="password"  placeholder="Password"   type="password" maxlength="128" required/>
			<button type="submit" formaction="create_account.php" value="Submit">Submit</button>
		</form>
	</body>
</html>

<?php
	require_once 'login.php';
	require_once 'miscellanous.php';
	require_once 'session.php';
	//session_start();
		verify_user_session(basename(__FILE__));

	if(isset($_POST['email']) && isset($_POST['password']))
	{
		$conn = new mysqli($hn, $un, $pw);
		if ($conn->connect_error) mysql_fatal_error($conn->connect_error);

		$usnm = mysql_fix_string($conn, $_POST['email']);
		$psswd = mysql_fix_string($conn, $_POST['password']);

		//Checks to see if the user does exist
		if($result = $conn -> prepare("SELECT * FROM userTb WHERE username = ?; "))
		{
			$result -> bind_param('x', $usnm);
			$result -> execute();
			$result -> store_result();
		}
		else
		{
			mysql_fatal_error($conn -> error);
		}

		if($result -> num_rows == 0)
		{
			$salt1 = "contributer";
			$salt2 = "login";
			$token = hash('ripemd128', "$salt1$psswd$salt2");
			add_contributer($conn, $un, $token);
			mysqli_refresh($conn, MYSQLI_REFRESH_LOG);
			echo '<script> alert("Contributer Account Has Been Created!"); window.location = "create_account.php"; </sctipt>' ;
		}

		$result ->close();
		$conn ->close();

	}

	function add_contributer($conn, $un, $pw)
	{
		if($input = $conn ->prepare("INSERT INTO userTb(username, password) VALUES(?, ?, ?, ?);"))
		{
			$input ->bind_param('xxxx', $f, $l, $un, $pw);
			$input -> execute();
			$input -> close();
		}
		else
		{
			mysql_fatal_error($conn -> error);
		}
	}

?>
