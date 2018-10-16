<?php

	include "db_connect.php";
	
	//yoes 20151118 -- aloow to specify return url
	$return_url = "index.php";
	
	if($_POST[return_url]){
		$return_url = $_POST[return_url];
	}
	
	
	if(strlen(trim($_POST["user_name"]))==0 || strlen(trim($_POST["password"]))==0){
	
		//nologin
		header("Location: ./".$return_url."?mode=error_pass");
	
	}else{
	
		//do login
		$user_name = doCleanInput($_POST["user_name"]);
		$password = doCleanInput($_POST["password"]);
		
		$query="SELECT * 
				FROM users
				WHERE user_name = '$user_name' 
				and user_password = '$password'
				and AccessLevel = 4 
				";
		
		//echo $query; exit();
		$post_row = getFirstRow($query);
		
		
		if ($post_row["user_id"]=="") {
			//no login
			header("Location: ".$return_url."?mode=error_pass");
			
		}elseif ($post_row["user_enabled"] == 0 && 1 == 0) {
			//pending
			//header("Location: ".$return_url."?mode=pending");
			
			//yoes 20160331 --> allow login even if pening
			
		}elseif ($post_row["user_enabled"] == 2) {
			//disabled
			header("Location: ".$return_url."?mode=disabled");
		}else{
			//have login
			session_start();
			
			
			$_SESSION['sess_userid'] = $post_row["user_id"];
			$_SESSION['sess_accesslevel'] = $post_row["AccessLevel"];
			$_SESSION['sess_meta'] = $post_row["user_meta"];
			
			
			//20160331 new session type for ejob
			$_SESSION['sess_user_enabled'] = $post_row["user_enabled"];
			
			//yoes 20141007
			$_SESSION['sess_can_manage_user'] = $post_row["user_can_manage_user"];
			
			if($post_row["FirstName"]){
				$_SESSION['sess_userfullname'] = $post_row["FirstName"] ." ". $post_row["LastName"];
			}else{
				$_SESSION['sess_userfullname'] = $post_row["user_name"];
			}
			
			
			
			
			//update last login datetime
			mysql_query("update users set LastLoginDatetime = now() where user_id = '".$post_row["user_id"]."'");
			
			
			//yoes 20151117
			//for admin -> go to dashboard first
			if($_SESSION['sess_accesslevel'] == 1 || $_SESSION['sess_accesslevel'] == 2 || $_SESSION['sess_accesslevel'] == 3 || $_SESSION['sess_accesslevel'] == 5){
				header("Location: dashboard.php");
				exit();
			}
			
			if($_POST["cont"]){
				header("Location: organization.php?id=".$_POST["cont"]);
			}else{			
				header("Location: org_list.php");
			}
		}
	
	}
	

?>