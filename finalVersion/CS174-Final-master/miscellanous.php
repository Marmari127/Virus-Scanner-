<?php

	function mysql_fatal_error($message) 
	{
		$msg = mysql_error();
		echo <<< _END
		Your request could not be processed at this time. Please try again. 
_END;
}
	//sanitize
	function mysql_entities_fix_string($conn, $string) 
	{
		return htmlentities(mysql_fix_string($conn, $string));
	}
	
	function mysql_fix_string($conn, $string) 
	{
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $conn->real_escape_string($string);
	}
?>