<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	//table name
	
	$table_name = "lawful_employees";
	
	
	//for company, record this to company table instead
	if($sess_accesslevel == 4){
			$table_name = "lawful_employees_company";
	}
	
	
	/*if($_POST[case_closed]){
			//yoes 20160118 --> extra 33 after case's closed
			$table_name = "lawful_employees_extra";
	}*/
	
	
	$this_cid = doCleanInput($_POST["le_cid"]);
	
		
		
		
	
	//yoes 20160201 --- sepcial case for custom education
	if($_POST[le_position] == 23){		
		$_POST[le_position] = doCleanInput($_POST[le_position_other]);
	}
	
	if($_POST[le_education] == 10){		
		$_POST[le_education] = doCleanInput($_POST[le_education_other]);
	}
	
	
	
	//specify all posts fields
	$input_fields = array(
						
						'le_name'
						,'le_gender'
						,'le_age'
						
						
						,'le_position'
						,'le_cid'
						
						,'le_year'
						
						,'le_wage_unit'
						
						,'le_education'

						);
					
	
	$le_date = $_POST["le_date_year"]."-".$_POST["le_date_month"]."-".$_POST["le_date_day"];
	
	$special_fields = array('le_start_date','le_wage', 'le_disable_desc');
	$special_values = array("'$le_date'","'".deleteCommas($_POST["le_wage"])."'","'".doCleanInput($_POST["le_disable_desc_hire"])."'");
	
	
	//yoes 20160126 --> check if le_id existed?
	$this_le_row = getFirstRow("select le_id, le_created_date, le_created_by from lawful_employees where le_year = '".$_POST["le_year"]."' and le_cid = '".$_POST["le_cid"]."'");
	if(!$this_le_row){
	
		//new record -> add created date
		array_push($special_fields,'le_created_date');
		array_push($special_values,'now()');
		
		array_push($special_fields,'le_created_by');
		array_push($special_values, $sess_userid);
			
	}else{
		
		array_push($special_fields,'le_created_date');
		array_push($special_values,"'".$this_le_row[le_created_date]."'");
		
		array_push($special_fields,'le_created_by');
		array_push($special_values, $sess_userid);
		
		
	}
	
	
	//add vars to db	
	if($_POST["le_id"]){
		array_push($special_fields, "le_id");
		array_push($special_values, "'".$_POST["le_id"]*1 ."'");
	}
	
	
	//yoes 20151122
	//lawful_employees from this script is always a "real" employees
	array_push($special_fields, "le_is_dummy_row");
	array_push($special_values, 0);
	
	
	//"new" le code
	$le_id = "";
	
	for($i=1;$i<=13;$i++){
		$le_id .= $_POST["leid_".$i];
	}
	
	//
	if($le_id){
		array_push($special_fields, "le_code");
		array_push($special_values, "'".$le_id*1 ."'");
	}
	
	
	
	
	//yoes 20140910 --- check if id existed in DB	
	//include "scrp_check_mn_des_person.php";
	
	
	if($have_record_in_oracle){ // $have_record_in_oracle are from scrp_check_mn_des_person.php
	
		array_push($special_fields, "le_from_oracle");
		array_push($special_values, "'1'");
		
	}
	
	
	//yoes 20150617 - also check if this is actually a "modified" data (or someone just press save without modifying anythin...)
	
	$the_sql = generateCheckRowExistedSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, "");
	
	$row_existed = getFirstItem($the_sql);
	//echo $the_sql; echo $row_existed; exit();
	
	
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
			
	//echo $the_sql;exit();
	mysql_query($the_sql);
	
	$inserted_id = mysql_insert_id();
	
	
	
	
	//sync this value to lawful employee
	$hire_numofemp = getFirstItem("
									SELECT 
										count(*)
									FROM 
										$table_name
									where
										le_cid = '".$_POST["le_cid"]."'
										and le_year = '".$_POST["le_year"]."'");
								
				
	if($sess_accesslevel == 4){
		
		$the_sql = ("update 
						lawfulness_company
					set 
						Hire_NumofEmp = '$hire_numofemp' 
					where 
						Year = '".$_POST["le_year"]."' 
						and 
						CID = '".$_POST["le_cid"]."'");		
						
		$autopost = 0; //company never auto-post
		
		mysql_query($the_sql);
								
	}
	
	
	
	// yoes 20160427 --
	//---> handle attached files
	$file_fields = array(
						"docfile_33_1"
						, "docfile_33_2"
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		//echo "filesize: ".$hire_docfile_size;
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		if($hire_docfile_size > 0){
			
			//echo "what";
		
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			//new file name
			$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
			$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
			//echo $hire_docfile_path;
			//
			
			if($hire_docfile_type == "image/jpeg" || $hire_docfile_type == "image/gif"
						|| $hire_docfile_type == "image/png" || $hire_docfile_type == "application/pdf"
			
			){
			
				if(move_uploaded_file($_FILES[$file_fields[$i]]['tmp_name'], $hire_docfile_path)){	
					//move upload file finished
					//array_push($special_fields,$file_fields[$i]);
					//array_push($special_values,"'".$new_hire_docfile_name.".".$hire_docfile_extension."'");
					
					$sql = "insert into files(
							file_name
							, file_for
							, file_type)
						values(
							'".$new_hire_docfile_name.".".$hire_docfile_extension."'
							,'$inserted_id'
							,'".$file_fields[$i]."'
						)";
				
					mysql_query($sql);
					
				}
			
			}else{
				
				//do nothing	
				
			}
			
		}else{
			
			
			
		
		}
	
	}		
	
	
	

	//yoes 20150617 - only do this if this a "new" data - and not just save it...
	if(!$row_existed && 1==0){ //yoes 20160402 -- no need to do this for company
		//also add modify stats
		//$history_sql = "insert into modify_history values('$sess_userid','$this_cid',now(), 2)";
		//mysql_query($history_sql);
		$lawful_id = getFirstItem("select lid from lawfulness where year = '".$_POST["le_year"]."' and cid = '".$_POST["le_cid"]."'");
		doAddModifyHistory($sess_userid,$this_cid,2,$lawful_id);
		
		//also add full-log here
		//$this_le_id = getFirstItem("select le_id from lawful_employees where le_year = '".$_POST["le_year"]."' and le_cid = '".$_POST["le_cid"]."'");
		//echo "select le_id from lawful_employees le_year = '".$_POST["le_year"]."' and le_cid = '".$_POST["le_cid"]."'"; exit();
		doLawfulEmployeesFullLog($sess_userid, $_POST["le_id"], basename($_SERVER["SCRIPT_FILENAME"]));
		
		//yoes 20160208
		resetLawfulnessByLID($lawful_id);
	}
	
	
	if($_POST["le_year"] >= 2013 || $is_2013){
		
		//do auto post when >= 2013 only
		header("location: organization.php?id=$this_cid&le=le&focus=lawful&year=".$_POST["le_year"]."&auto_post=$autopost");
	
	}else{
		
		//yoes 20151222 --> try this auto post thing
		//header("location: organization.php?id=$this_cid&le=le&focus=lawful&year=".$_POST["le_year"]."");		
		header("location: organization.php?id=$this_cid&le=le&focus=lawful&year=".$_POST["le_year"]."&auto_post=$autopost");
		
	}

?>