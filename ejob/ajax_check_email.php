<?php

	include "db_connect.php";
	
	
	if($_POST["email"]){
		//$this_id = doCleanInput($_POST["id"]);
		$email = doCleanInput($_POST["email"]);
		//$cid = getFirstItem("select cid from company where companyCode = '".doCleanInput($_POST["cid"])) . "'";
		
	}else{
		exit();
	}
	
				
	//echo trim(getFirstItem("select count(*) from users where user_email = '$email' and cid != '$cid'"));
	echo trim(getFirstItem("select count(*) from users where user_email = '$email'"));

?>