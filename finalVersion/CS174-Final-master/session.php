<?php

	function verify_user_session($page) 
	{
		session_start();

		if (notCurrentUser()) 
		{
			kill_session();
			echo '<script> alert("For your safety, please log in again."); window.location = "authenticate_users.php"; </script>';
		}
		else if ((isset($_SESSION['user']) || isset($_SESSION['admin'])) && $current_page !== 'file_upload.php') 
		{
			header('Login');
			exit;
		}
		else if ((!isset($_SESSION['user']) || !isset($_SESSION['admin'])) && $current_page === 'file_upload.php') 
		{
			header('Login');
		}		
		else
		{
			session_destroy();
		}
	}
	
	function notCurrentUser() 
	{

		if(isset($_SESSION['ip']) && isset($_SERVER['REMOTE_ADDR']) && isset($_SESSION['ua']) && isset($_SERVER['HTTP_USER_AGENT']) && isset($_SESSION['check']) && isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['HTTP_USER_AGENT'])) 
		{
			if ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] || $_SESSION['ua'] != $_SERVER['HTTP_USER_AGENT'] ||  $_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']))
			{
				return true;
			}
		}
		
		return false;
	}
	
	function kill_session() 
	{
		$_SESSION = array();
		setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
		header('Login');
	}
?>