<?php

	include "db_connect.php";
	
	//parameters
	$the_lid = doCleanInput($_POST["the_lid"]);
	$the_cid = doCleanInput($_POST["the_cid"]);
	$the_year = doCleanInput($_POST["the_year"]);
	
	//echo "<br>".$the_lid;
	//echo "<br>".$the_cid;
	//echo "<br>".$the_year;
	
	//exit();
	
	
	
	//Transfer data from _company to real table

	////
	//1. start with number of employees 
	////
	
	$sql = "
			
			
				UPDATE 
					lawfulness a 
				JOIN 				
					lawfulness_company b 

				ON 
					a.LID = b.LID 
				
				SET 
					a.Employees = b.Employees
					, a.Hire_NumOfEmp = b.Hire_NumOfEmp
				
				
				where	
					a.LID = '$the_lid'
	
			";
			
	mysql_query($sql);
	
	
	
	
	//yoes - NEW as of 20151021
	//also update lawfulness.Employees equals to value got from "All branches" instead
	
	//the_sum_employees
	$sql = "
			
			
				UPDATE 
					lawfulness a 
				
				SET 
					a.Employees = '".$_POST["the_sum_employees"]."'				
				
				where	
					a.LID = '$the_lid'
	
			";
			
	//echo $sql; exit();
			
	mysql_query($sql);
	
	
	
	//yoes 20151021
	//also transfer branch data from temp "company table" to real table
	//but only do so if this is "current" fiscal year
	if(date("m") >= 9){
		$the_end_year = date("Y")+1; //new year at month 9
	}else{
		$the_end_year = date("Y");
	}
	
	//
	//echo $the_year . $the_end_year; exit();
	
	
	//only do this on the latest year
	if($the_year == $the_end_year){
	
		//try get branch info
		
		$result_set_sql = "
					select 
						* 
					from 
						company_employees_company 
					where 						
						lawful_year = '$the_year'
						
						and
						
						cid in (
						
							select 
								cid
							from							
								company 
							where 
								CompanyCode = '". getFirstItem("select CompanyCode from company where cid = '$the_cid'")."' 
							
						
						)
						
					";	
				
		//echo $result_set_sql; exit();
		
		$result_set = mysql_query($result_set_sql);
		
		while ($result = mysql_fetch_array($result_set)) {
			

			$sql = "
					update 
						company 
					set 
						employees = '".$result["employees"]."' 
					where 
						cid = '".$result["cid"]."' 
					
					";			
			mysql_query($sql);
			
			$sql = "
					update ignore
						company_employees_company 
					set 
						lawful_year = lawful_year +1000 
					where 
						cid = '".$result["cid"]."' 
						and
						lawful_year = '$the_year'
					";			
			mysql_query($sql);

		}	
		
		
		
		
		
		//also transfer company_company to real company
		
		$result_set_sql = "
		
			select 
				*
			from							
				company_company
			where 
				CompanyCode = '". getFirstItem("select CompanyCode from company where cid = '$the_cid'")."' 

		
		";
		
		
		$result_set = mysql_query($result_set_sql);
		
		while ($result = mysql_fetch_array($result_set)) {
			

			$_POST['CompanyCode'] = $result['CompanyCode'];
			
			//$_POST['CompanyCode'] = $result[];
			
			$_POST['BranchCode'] = $result[BranchCode];
			
			
			$_POST['CompanyNameThai'] = $result[CompanyNameThai];
			$_POST['CompanyNameEng'] = $result[CompanyNameEng];
			$_POST['Address1'] = $result[Address1];
			$_POST['Moo'] = $result[Moo];
			$_POST['Soi'] = $result[Soi];
			
			
			$_POST['Road'] = $result[Road];
			$_POST['Subdistrict'] = $result[Subdistrict];
			$_POST['District'] = $result[District];
			$_POST['Province'] = $result[Province];
			$_POST['Zip'] = $result[Zip];
			
			$_POST['CompanyTypeCode'] = getFirstItem("select CompanyTypeCode from company where cid = '$the_cid'");
			$_POST['BusinessTypeCode'] = getFirstItem("select BusinessTypeCode from company where cid = '$the_cid'");
			$_POST['Status'] = 1;
			
			$_POST['Employees'] = $result['Employees'];
			
			$table_name = "company";
			
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
								
								
								,'CompanyTypeCode'
								,'BranchCode'
								
								,'BusinessTypeCode'
								
								,'Status'
														
								);
							
			//fields not from $_post	
			$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees", "CreatedDateTime","CreatedBy", "is_active_branch");
			$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'","NOW()","'$sess_userid'","1");
			
			//add vars to db
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, "insert ignore");
					
			//echo "<br>".$the_sql;
			
				
			mysql_query($the_sql);

		}	//end while
		
		
		//yoes 20151122 --> delete data from company after the fact
		$delete_the_sql = "
		
			delete
			from							
				company_company
			where 
				CompanyCode = '". getFirstItem("select CompanyCode from company where cid = '$the_cid'")."' 

		
		";
		
		
		mysql_query($delete_the_sql);
		
		
	}//end if for if($the_year == $the_end_year){
	
	//exit();
	
	
	/////////
	/////// number of employees moved - delete it
	/////////	
	//mysql_query("delete from lawfulness_company where LID = '$the_lid'");
	
	
	
	////
	//
	//	2# then do lawful employees
	//
	////
	
	
	$sql = "
	
			insert into
				lawful_employees(
				
				 	le_name
					,le_gender
					,le_age
					,le_code
					,le_disable_desc
					,le_start_date
					,le_wage
					,le_position
					,le_year
					,le_cid
					,le_wage_unit	
					
					,le_from_oracle			
					
					,le_education
				
				)
			select
				le_name
				,le_gender
				,le_age
				,le_code
				,le_disable_desc
				,le_start_date
				,le_wage
				,le_position
				,le_year
				,le_cid
				,le_wage_unit	
				
				,le_from_oracle
				
				,le_education
			from 				
				lawful_employees_company
			where	
				le_cid = '$the_cid'
				and
				le_year = '$the_year'
	
			";
	
	//echo $sql; exit();
	mysql_query($sql);
	
	//yoes 20160501 --- also send files
	
	$sql = "
	
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.le_id
				, a.file_type
			from
				files a
					join
						lawful_employees_company b 
					on
						a.file_for = b.le_id
						and
						(
							a.file_type = 'docfile_33_1'
							or 
							a.file_type = 'docfile_33_2'
						)
						and
						b.le_cid = '$the_cid'
						and
						b.le_year = '$the_year'
					
					join
						lawful_employees c
					on
						b.le_code = c.le_code
						and
						b.le_cid = c.le_cid
						and
						b.le_year = c.le_year
						
			
		
	
	";
	
	
	
	//echo $sql; exit();
	mysql_query($sql);
	
	//exit();
	
	//yoes 20160501 then transfer the rest of 33 files
	
	$sql = "
		
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.lid
				, concat(a.file_type, '_adm')
			from
				files a
					join
						lawfulness_company b 
					on
						a.file_for = b.lid
						and
						(
							a.file_type = 'company_33_docfile_3'
							or 
							a.file_type = 'company_33_docfile_4'
							or 
							a.file_type = 'company_33_docfile_5'
							or 
							a.file_type = 'company_33_docfile_6'
							or 
							a.file_type = 'company_33_docfile_7'
						)
						and
						b.cid = '$the_cid'
						and
						b.year = '$the_year'
					
					join
						lawfulness_company c
					on
						b.lid = c.lid
						and
						b.cid = c.cid
						and
						b.year = c.year
	
	
	";
	
	
	//echo $sql; exit();
	mysql_query($sql);
	
	/// delete the transferred info
	//mysql_query("delete from lawful_employees_company where le_cid = '$the_cid' and le_year = '$the_year'");
	
	
	
	
	//yoes 20160501 -- then attach file for m34
	$sql = "
		
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.lid
				, concat(a.file_type, '_adm')
			from
				files a
					join
						lawfulness_company b 
					on
						a.file_for = b.lid
						and
						(
							a.file_type = 'company_34_docfile_1'							
						)
						and
						b.cid = '$the_cid'
						and
						b.year = '$the_year'
					
					join
						lawfulness_company c
					on
						b.lid = c.lid
						and
						b.cid = c.cid
						and
						b.year = c.year
	
	
	";
	
	
	//echo $sql; exit();
	mysql_query($sql);
	//exit();
	
	////
	//
	//	3# then do CURATOR
	//
	////
	
	
	
	
	//echo $sql; exit();
	//mysql_query($sql);
	
	//First -> do parent curator...
	$sql = "select * from curator_company where curator_lid = '$the_lid' and curator_parent = '0'";
	
	//echo $sql; exit();
	
	$sub_result = mysql_query($sql);
	
	while ($sub_row = mysql_fetch_array($sub_result)) {			

		//$total_sub++;
		//add parent
		$sql = "
	
				insert into
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					
					
					)
					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					from 
						curator_company
					where
						curator_id = '".$sub_row["curator_id"]."'
	
				";
		
		//echo "<br>". $sql; exit();
		
		mysql_query($sql);
		
		//last inserted ID to "real" data
		$last_id = mysql_insert_id();		
		
		
	
	
	
		//also send file for this curator
		
		//after add parent, see if have child
		$child_sql = "select * from curator_company where curator_parent = '".$sub_row["curator_id"]."'";
		
		$child_result = mysql_query($child_sql);
		
		while ($child_row = mysql_fetch_array($child_result)) {		
			
			//if have any child....
			$sql = "
	
				insert into
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					
					
					)
					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,'$last_id'
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					from 
						curator_company
					where
						curator_id = '".$child_row["curator_id"]."'
	
				";
			
			//add child..	
			mysql_query($sql);
			
		}
		
	}
	
	
	/// delete the transferred info
	//mysql_query("delete from curator_company where curator_lid = '$the_lid'");
	
	//yoes 20160501
	$sql = "
	
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.curator_id
				, a.file_type
			from
				files a
					join
						curator_company b 
					on
						a.file_for = b.curator_id
						and
						(
							a.file_type = 'curator_docfile'
							or 
							a.file_type = 'curator_docfile_2'
							or 
							a.file_type = 'curator_docfile_3'
						)
						and
						b.curator_lid = '$the_lid'
					
					join
						curator c
					on
						b.curator_lid = c.curator_lid
						and
						b.curator_idcard = c.curator_idcard
						
		
	
	";
	
	
	mysql_query($sql);
	
	//exit();
	
	
	//update flag so we know we've moved this company's data
	$sql = "update 
			lawfulness_company 
			set 
				lawful_submitted = 2
				, lawful_approved_on = now() 
				, lawful_approved_by = '$sess_userid'
				
				where Year = '$the_year' and CID = '$the_cid'";
	mysql_query($sql);
	
	
	
	//yoes 20151123
	//also send mail to company's user
	
	$user_row = getFirstRow("
						
						select 
							user_email 
						from 
							users 
						where 
							user_meta = '$the_cid' 
							and 
							AccessLevel = 4 
							and user_enabled = 1
						limit
							0,1
						
						");
						
	$mail_address = $user_row[user_email];
	//
				
	$the_header = "ระบบรายงานผลการจ้างงานคนพิการ: ผู้ดูแลระบบได้รับข้อมูลการปฏิบัติตามกฏหมายแล้ว";

	$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($mail_address["FirstName"])." ".doCleanInput($mail_address["LastName"])."<br><br>";

	$the_body .= "ผู้ดูแลระบบได้รับข้อมูลการปฏิบัติตามกฏหมายของคุณแล้ว <br>";
	$the_body .= "หลังจากมีการตรวจสอบข้อมูลที่เกี่ยวข้องแล้ว จะมีการแจ้งสถานะการปฏิบัติตามกฏหมายไปทาง email นี้อีกครั้ง<br><br>";


	$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
	
	
	if ($server_ip == "203.146.215.187"){
		//ictmerlin.com use default mail
		mail($mail_address, $the_header, $the_body);
	}elseif ($server_ip == "127.0.0.1"){
	
		//donothin	
	
	}else{
		//use smtp
		doSendMail($mail_address, $the_header, $the_body);	
	}
	
	
	//that's that..........
	//do a redirect backkkkkkkk...........
	
	//yoes 20160208
	resetLawfulnessByLID($the_lid);
	
	
	
	//yoes 20180319
	//also mark document_requests as "done"
		
	$sql = "
	
		select
			docr_id
		from
			document_requests
		where
			docr_org_id = '$the_cid'
			and
			docr_year = '$the_year'
		order by
			docr_id desc
		limit 0,1
		
	
	";
	
	
	$the_docr_id = getFirstItem($sql);
	
	if($the_docr_id){
		
		$sql = "update document_requests set docr_status = 1 where docr_id = '$the_docr_id'";
		mysql_query($sql);
		
	}else{
		
		$sql = "
		
			insert into 
				document_requests(
					docr_org_id
					,docr_status					
					,docr_year
					,docr_last_updated
					,docr_date
				)values( 
					'$the_cid'
					,'1'
					,'$the_year'
					,NOW()					
					,NOW()
					
					)
		
		";
		mysql_query($sql);
		
	}
	
	
	header("location: organization.php?id=$the_cid&focus=lawful&year=".$the_year."&auto_post=1");

	

?>