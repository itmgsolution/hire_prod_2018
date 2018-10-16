<?php

//error_reporting(1);

//ini_set('post_max_size', '5M');
ini_set('upload_max_filesize', '5M');

//yoes 20141007
$server_ip=$_SERVER[SERVER_ADDR];

$host = "localhost";
$db = "hire_project";
$username = "root";
$password = "password";
$password = "N9Af0a596f7Cb";

//** local
$host = "localhost";
$db = "hire_project_20130520";
$username = "sanroku";
$password = "qwerty789";


$host = "localhost";
$db = "nep_web";
$username = "nep";
$password = "hKXj8tne";

//** local

//echo $server_ip; exit();

if ($server_ip == "127.0.0.1" || $server_ip == "::1"){
	
	$host = "localhost";
	$db = "hire_project";
	
	//$username = "sanroku";
	$username = "eservice";
	$password = "qwerty789";

}elseif ($server_ip == "203.146.215.187"){
	
	//ictmerlin
	$host = "localhost";
	$db = "nep_web";
	$username = "nep";
	$password = "hKXj8tne";
	

}else{

	//produciton
	/*
	$host = "production_db"; 
	//$host = "testing_db"; 
	$db="hire_project";
	$username = "eservice";
	//$password = "db@dmin+";
	//$password = "qwerty789";
	$password = "db@dmin+";
	*/
	
	
	//$host = "testing_db"; 
	$host = "production_db"; 
	$db="hire_project";
	$username = "dba";
	//$password = "db@dmin-";
	$password = "db@dmin+";
	
	
}

//echo $host;
//exit();

//echo $host . " - " . $username . " - ". $password; exit();

///

$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;
mysql_query("SET NAMES 'utf8'");

//handle session
session_start();

if(isset($_SESSION['sess_userid'])){
	
	$sess_userid = $_SESSION['sess_userid'];
	$sess_accesslevel = $_SESSION['sess_accesslevel'];
	$sess_meta = $_SESSION['sess_meta'];
	$sess_userfullname = $_SESSION['sess_userfullname'];
	
	$sess_user_enabled = $_SESSION['sess_user_enabled'];
	
	//yoes 20140710
	$sess_can_manage_user = $_SESSION['sess_can_manage_user'];
	
	//session handling?
	$this_script_name_array = explode("/",$_SERVER['SCRIPT_NAME']);
	$this_real_script_name = $this_script_name_array[count($this_script_name_array)-1];
	
	if($sess_accesslevel == 2){
	
		if(
			$this_real_script_name == "user_list.php"
			){
			header("location: index.php");
			exit();
		}
	
	}elseif($sess_accesslevel == 4){
	
		if(
			
			($this_real_script_name == "org_list.php" && $_GET["search_id"] != $sess_meta)
			
			){
			header("location: organization.php?id=$sess_meta");
			exit();
		}
	
	}
	
	//echo $this_real_script_name;
}

require_once "functions.php";

?>
