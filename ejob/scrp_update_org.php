<?php

	include "db_connect.php";
	include "session_handler.php";
	
	//table name
	$table_name = "company";
	$this_id = doCleanInput($_POST["CID"]);
	
	//yoes 20160105
	//add log before doing anything
	doCompanyFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]));	
	
	if($_POST["btn_delete"]){
	
	
		//yoes 20160126 --> add log before delete		
	
		//delete everything
		$the_sql = "delete from announcecomp where CID = '$this_id'";
		mysql_query($the_sql);
		
		$the_sql = "delete from docrequestcompany where CID = '$this_id'";
		mysql_query($the_sql);
		
		$the_sql = "delete from document_requests where docr_org_id = '$this_id'";
		mysql_query($the_sql);

		$the_sql = "delete from lawful_employees where le_cid = '$this_id'";
		mysql_query($the_sql);
		
		//delete payments of this LID
		$the_sql = "select LID from lawfulness where CID = '$this_id'";
		$the_result = mysql_query($the_sql);
		while($post_row = mysql_fetch_array($the_result)){
			
			//add full log for delete
			doLawfulnessFullLog($sess_userid, $post_row["LID"], basename($_SERVER["SCRIPT_FILENAME"]));
			
			$the_sql = "delete from payment where LID = '".$post_row["LID"]."'";
			mysql_query($the_sql);
		}
		
		$the_sql = "delete from lawfulness where CID = '$this_id'";
		mysql_query($the_sql);
		
		$the_sql = "delete from company where CID = '$this_id'";
		mysql_query($the_sql);
		
		header("location: org_list.php");
	
	}else{
	
		//do validation
		$the_code = doCleanInput($_POST["CompanyCode"]);
		$the_branch = doCleanInput($_POST["BranchCode"]);
	
		$existed_company_id = getFirstItem("select (CID) from company
							where 
							CompanyCode = '".$the_code."'
							and BranchCode = '".$the_branch."'"
							);
							
		//echo $count_company;exit();							
		if(strlen($existed_company_id) > 0){
						
			if($existed_company_id != $this_id){
				header("location: organization.php?id=$this_id&new_id=$the_code&new_id_link=$existed_company_id&branch=$the_branch" );
				exit();
			}
		}	
	
		//specify all posts fields
		$input_fields = array(
							
							'CompanyNameThai'
							,'CompanyNameEng'
							,'Address1'
							
							,'Moo'
							,'Soi'
							,'Road'
							,'Subdistrict'
							,'District'
							
							,'Province'
							,'Zip'
							,'Telephone'
							,'email'
							,'TaxID'
							
							,'CompanyTypeCode'
							,'BusinessTypeCode'
							
							,'org_website'
							
							,'Status'
							
							,'ContactPerson1'
							,'ContactPhone1'
							,'ContactEmail1'
							,'ContactPosition1'
							,'ContactPerson2'
							,'ContactPhone2'
							,'ContactEmail2'
							,'ContactPosition2'
							
							
							
							);
						
		if($sess_accesslevel != 4){
			//non-company user can push these
			array_push($input_fields,'CompanyCode','BranchCode');
		}
		
		//fields not from $_post	
		$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees","last_modified_lid_year"); //yoes 20160125 ---> more details
		$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'", "'".($_POST[the_year]*1)."'");
		
		//conditions
		$condition_sql = "where CID = '".$this_id."' limit 1";
		
		
		//yoes 20150617 - also check if this is actually a "modified" data (or someone just press save without modifying anythin...)
		$condition_sql_check_existed = " and CID = '".$this_id."'";
		$special_fields_check_existed = array("Employees");
		$special_values_check_existed = array("'".deleteCommas($_POST['Employees'])."'");
		$the_sql = generateCheckRowExistedSQL($_POST,$table_name,$input_fields,$special_fields_check_existed,$special_values_check_existed, $condition_sql_check_existed);
		$row_existed = getFirstItem($the_sql);
		
		
		
		//add vars to db
		//yoes 20150617 -> only add history if this is an actual "edit"
		if(!$row_existed){
			
			$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, $condition_sql);
			
			//echo $the_sql; exit();
			mysql_query($the_sql);
			
			//yoes 20160201
			$district_to_clean_cid = $this_id;
			include "scrp_update_district_cleaned_to_cid.php";
			
			//then add this to history
			//$history_sql = "insert into modify_history values('$sess_userid','$this_id',now(),0)";
			//mysql_query($history_sql);
			
			doAddModifyHistory($sess_userid,$this_id,0);
			
		}
		
		
		if($sess_accesslevel == 4){			
			//alert email to admin if company user update his info
			
			$formatted_name = formatCompanyName($_POST["CompanyNameThai"],$_POST["CompanyTypeCode"]);
			$company_province = getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
			
			$headers .= "Content-type: text/plain;charset=utf-8" . "\r\n";		
			
			mail("yoes@uklahouse.com,jazzining@gmail.com"
				, "มีการปรับปรุงข้อมูลกิจการของ $formatted_name : $company_province"
				, "มีการปรับปรุงข้อมูลกิจการของ $formatted_name : $company_province \n\nกดที่นี่เพื่อดูรายละเอียด (ต้อง login ก่อน): http://thaidrivingspirit.com/organization.php?id=$this_id"
				, $headers);
		
		}
		
		header("location: organization.php?id=$this_id&updated=updated");
		
	}

?>