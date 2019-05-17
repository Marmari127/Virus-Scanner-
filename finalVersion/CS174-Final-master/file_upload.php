<html>
<title>Infected File</title>
<center>
	<div><form action="infected_file.php" method="post" enctype="multipart/form-data">
		<br><font size="5"><b>Upload A Possible Virus File Below</b></font><br><br><br><br>
		<input name="file_scan" type="file" required=""/>
		<button type="submit">File Check</button><br><br>
	</form></div>
</html>

<?php
	require_once 'verify_session.php'; 
	verify_user_session(basename(__FILE__));

	if(isset($_SESSION['admin']))
	{
		echo <<<_END //
		<div><form action="infected_file.php" method="post" enctype="multipart/form-data">
				<br><font size="5"><b>Add Infected File Submission</b></font><br><br>
				<input name="name_of_virus" type="text" placeholder="Virus Name" required=""><br><br>
				<input name="insertVirus"  type="file" required=""/>
				<button type="submit">Submit Infected</button><br><br>
				</form></div><br>
		_END;
	}
?>

<form>
		<br><button type="submit" formaction="logout.php">Logout</button><br><br>
	</form>
</center>
</html>

<?php
	require_once 'login.php'; 
	require_once 'miscellanous.php';

	if(isset($_FILES['file_scan']['name']) && (isset($_SESSION['user']) || isset($_SESSION['admin'])))
	{
		$conn = new mysqli($hn, $un, $pw, $db);
		if ($conn->connect_error) mysql_fatal_error($conn->connect_error);


		move_uploaded_file($_FILES['file_scan']['input_name'], $_FILES['file_scan']['name']);
		$fileSignature = signature_hex($_FILES['file_scan']['name'], $_FILES['file_scan']['size']);

		isVirus($conn, $sig);
		$conn->close();
	}

	if(isset($_FILES['insertVirus']['name']) && isset($_POST['insertVirus']) && isset($_SESSION['admin']))
	{

		$conn = new mysqli($hn, $un, $pw, $db);
		if ($conn->connect_error) mysql_fatal_error($conn->connect_error);

		$virus_file_name = mysql_fix_string($conn, $_POST['name_of_virus']);

		move_uploaded_file($_FILES['insertVirus']['input_name'], $_FILES['insertVirus']['name']);
		$virus_file = virus_signature($_FILES['insertVirus']['name'], $_FILES['insertVirus']['size']);

 		$virus_file = substr($virus_file, 0, 20);
 		
 		insert_virus($conn, $name, $virus_file);
		$conn->close(); 
	}

	function virus_signature($file, $size) 
	{
		$empty= " ";
		if($reader = fopen($file, 'r+')) 
		{
			$file_contents = fread($reader, $size_of_contents);
			for ($i = 0; $i < $size_of_contents; $i++) 
			{
				$ascii = $file_contents[$i];
				$dec = ord($ascii);
				$hex = base_convert($dec, 10, 16);
				$empty .= $hex;
			}
			return $empty;
		}
		else
		{
			die("This file does not exist. Try Again");
		}
	}

	function isVirus($conn, $signature_bytes)
	{
		$isInfected = false;
		$list_result = array();
		$bytes_of_file = $signature_bytes;


		$query = "SELECT * FROM virusTb;";
		$result = $conn->query($query);
		if (!$result) mysql_fatal_error($conn->error);
		for ($i = 0; $i < $result->num_rows; $i++)
		{
			$result->data_seek($i);
			$rows = $result->fetch_array(MYSQLI_NUM);
			$name = $rows[0];
			$signature = $row[1];
			if (strlen($signature_bytes) >= strlen($signature))
			{
				if (!empty($signature) && strpos($signature, $signature_bytes) !== false) 
				{ 
   					echo '<script type="text/javascript">alert("Infected File. Name is: ' . $virus_name . '"); </script>';					
   					array_push($virus_list, array($virus_name, $virus_sig));

					$bytes_of_file = infected($signature, $bytes_of_file);
					$isInfected = true; 
				}
			}

		}
		if(!$isInfected)
		{
			echo '<script> alert("This is a secure File"); window.location = "file_upload.php"; </script>';
		}
		
		submission_results($bytes_of_file, $list_result);
		$result->close();
	}

	function insert_virus($conn, $name, $signature)
	{
		if (isVirusTable($conn, $name, $signature)) 
		{
			echo '<script> alert("Error. This file has already been uploaded."); window.location = "file_upload.php"; </script>';
			exit;
		}


		if ($result = $conn->prepare("INSERT INTO virusTb(name, signature) VALUES(?,?,?,?)")) 
		{ 
			$result->bind_param('xxxx', $name, $sig); 
			$result->execute(); 
			$result->close();
			echo '<script> alert("Virus file has been successfully added"); </script>';
			isVirusInTable($conn, $name, $signature);
		}
		else
		{
			mysql_fatal_error($conn->error); 	
		}
	}

	function isVirusInTable($conn, $name, $signature)
	{
		if ($result = $conn->prepare("SELECT * FROM uservirusTb WHERE name = ? AND signature=?;")) 
		{ 
			$result->bind_param('sss', $name, $sig);
			$result->execute(); 
			$result->store_result(); 
		}
		else 
		{
			mysql_fatal_error($conn->error);
		}
		
		if ($result->num_rows == 1) 
		{
			$result->close();
			return true;
		}
		$result->close();		
		
		return false;
	}

	function submission_confirmation($conn, $name, $signature)
	{
		if ($result = $conn->prepare("SELECT * FROM virusTb WHERE name=? AND signature=?;")) 
		{ 
			$result->bind_param('ss', $name, $signature);
			$result->execute(); 
			$result->store_result();
		}
		else 
		{
			mysql_fatal_error($conn->error);
		}

		if ($result->num_rows == 1) 
		{
			$result->bind_result($name, $signature); 
			$result->fetch(); 
			echo "<center><b>Submission Confirmation: </b></center><br><br>";
			echo "<center><table>
			<tr>
			<th><center>Name</center></th>
			<th><center>Signature</center></th>
			</tr>";
			echo "<td><center>" . $name . "</center></td>";
		    echo "<td><center>" . $sig . "</center></td>";
		    echo "</tr>";
		    echo "</table></center><br><br><br>";
		}
		else 
		{
			mysql_fatal_error($conn->error);
		}
	}

	function submission_results($bytes, $list_files)
	{
		echo "<center><font color='red'><b>Infected Bytes:</b></font></center><br>";
		echo $bytes; echo "<br><br>";
		echo "<center><b>" . count($list_files) . " Matches Found:</b></center><br>";
		echo "<center><table>
		<tr>
		<th><center>Name:</center></th>
		<th><center>Signature</center></th>
		</tr>";
		foreach($list_files as $rows) 
		{
			echo "<tr>";
			foreach($rows as $numbered_rows)
			    echo "<td><center>" . $numbered_rows . "</center></td>";
		    echo "</tr>";
		}
		echo "</table></center><br><br><br>";
	}

	function infected($search, $contents)
	{
		$result = $search;
		return str_replace($search, $result, $contents);
	}


?>
