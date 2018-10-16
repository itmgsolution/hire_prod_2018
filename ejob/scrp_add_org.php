<?php

	include "db_connect.php";
	
	//table name
	$table_name = "company";
	
	//first, validate company code
	$the_code = doCleanInput($_POST["CompanyCode"]);
	$the_branch = doCleanInput($_POST["BranchCode"]);
	
	$count_company = getFirstItem("select CID from company
						where 
							CompanyCode = '".$the_code."'
							and BranchCode = '".$the_branch."'
							
							");
	if(strlen($count_company) > 0){
		//come to this page via lawful tab
		header("location: organization.php?mode=new&new_id=$the_code&new_id_link=$count_company&branch=$the_branch" );
		exit();
	}						
	
	//specify all posts fields
	$input_fields = array(
						'CompanyCode'
						,'CompanyNameThai'
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
						,'BranchCode'
						,'org_website'
						,'BusinessTypeCode'
						
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
					
	//fields not from $_post	
	$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees", "CreatedDateTime","CreatedBy", "is_active_branch");
	$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'","NOW()","'$sess_userid'","1");
	
	//add vars to db
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	$this_id = mysql_insert_id();
	
	
	/*
	$the_end_year = date("Y")+10;
	for($i= $the_end_year;$i>=2007;$i--){
	
		//also generate defaul lawfulness
		$the_sql = "insert ignore into lawfulness(CID, Year, employees) values('$this_id', '$i','".deleteCommas($_POST['Employees'])."')";
		
		mysql_query($the_sql);
	
	}*/
	
	//yoes 20160201
	$district_to_clean_cid = $this_id;
	include "scrp_update_district_cleaned_to_cid.php";
	
	
	//what year to create a lawfulness for
	$lawful_year = $_POST["ddl_year"];
	
	$the_sql = "insert ignore into lawfulness(CID, Year, employees) values('$this_id', '$lawful_year','".deleteCommas($_POST['Employees'])."')";	
	mysql_query($the_sql);
	
	
	header("location: organization.php?id=$this_id&added=added");

?>