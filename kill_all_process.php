<?php

	include "db_connect.php";
	
	$result = mysql_query("SHOW FULL PROCESSLIST");
	while ($row=mysql_fetch_array($result)) {
		
		$i++;
		echo $i; 
	
	  $process_id=$row["Id"];
	  if ($row["Time"] > 5 ) {
		$sql="KILL $process_id";
		mysql_query($sql);
	  }
	}


?>All process cleared!?