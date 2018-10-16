<?php

	include "db_connect.php";
	//include "session_handler.php";
	
	if(is_numeric($_POST["id"]) && $_POST["yoes"]=="san"){
		$this_id = doCleanInput($_POST["id"]);
	}else{
		exit();
	}
	
	//table name
	
	$the_sql = "
	
				delete from files
				where 
					file_id = '$this_id'
				limit 1
				
				";
	
	mysql_query($the_sql);
				
	echo trim("$this_id");

?>