<?php

	include "db_connect.php";
	
	$the_id = "9999999999";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	
	
	///
	$company_row = getFirstRow("select CompanyNameThai,CompanyTypeCode, CID from company where CompanyCode = '$the_id' and BranchCode < 1");
	

	//yoes 20160427 
	//also check 1 user per company only
	$user_count = getFirstItem("select count(*) from users where user_meta = '".$company_row["CID"]."' and user_enabled != 2 and AccessLevel = 4");

	
			
	if($company_row){	
	
		$the_output = "someVar = { 
					'company_name_thai' : '". formatCompanyName($company_row["CompanyNameThai"], $company_row["CompanyTypeCode"])."'
					, 'company_cid' : '".$company_row["CID"]."'
					, 'user_count' : '".$user_count."'
					}";
		
		echo $the_output; 	
	}else{
		echo "no_result";
	}

?>