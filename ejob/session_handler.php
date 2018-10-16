<?php

session_start();
if(isset($_SESSION['sess_userid'])){
	$sess_userid = $_SESSION['sess_userid'];
}
if(!isset($sess_userid)){
	
	if(isset($_GET["id"])){
		$back_to = $_GET["id"];
		//echo $back_to; exit();
		$header_to_use = 'location: index.php?cont='.$back_to;
	}else{
		$header_to_use = "location: index.php";
	}
	
	header($header_to_use);
}
//echo $sess_userid;


//yoes 20160331
//not-enabled user go to amend page by default
if($sess_user_enabled == 0 || $sess_user_enabled == 2 || $sess_user_enabled == 9){
	header("location: view_register.php");		
	exit();
}

?>

	
	
