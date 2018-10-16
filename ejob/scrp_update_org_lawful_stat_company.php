<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	
	//include "scrp_config.php";
	
	//table name
	$table_name = "lawfulness_company";
	$this_id = doCleanInput($_POST["CID"]);
	$this_year = doCleanInput($_POST["Year"]);
	
	$lawful_id = getFirstItem("select LID from lawfulness_company where Year = '$this_year' and CID = '$this_id'");
	
	//echo "select LID from lawfulness_company where Year = '$this_year' and CID = '$this_id'";	
	//echo $this_year . "-" . $this_id . "-" . $lawful_id; exit();
	
	//20140306 -- also add this
	$current_is_submitted = getFirstItem("select lawful_submitted from lawfulness_company where Year = '$this_year' and CID = '$this_id'");
	
	
	if($current_is_submitted < 1){
		$is_submitted = $_POST["is_submitted"];
	}else{
		$is_submitted = $current_is_submitted;
	}
	
	
	

	
	//for COMPANY
	//JUST UPDATE flag to notify admin
	
	$sql = "update 
				lawfulness_company 
			set 
				lawful_submitted = '$is_submitted'
				, lawful_remarks = '".doCleaninput($_POST["lawful_remarks"])."' 
				, lawful_submitted_on = now()
			where 
				Year = '$this_year' and CID = '$this_id'";
	
	//echo $sql; exit();
	
	
	
	//yoes 20151123 --
	//also sending out email when company ยื่นแบบฟอร์มออนไลน์
	
	if($is_submitted == 1){
		
		
		$company_row = getFirstRow("select * from company where cid = '$this_id'");
		$company_name = doCleanInput($company_row["CompanyNameThai"]);
		
		
		//yoes -- see what zone is this company  is in....
		$user_id_of_zone = getFirstItem("
			
			select 
				user_id
			from
				zone_user
			where
				zone_id = (
			
						select 
							zone_id
						from 
							zone_district
						where
							district_area_code = (
						
								select
									district_area_code
								from
									districts
								where
									(
										district_name = '".doCleanInput($company_row[District])."'
										or
										district_name = '".doCleanInput($company_row[District_cleaned])."'
									)
									and
									province_code in (
									
										select 
											province_code
										from 
											provinces
										where
											province_id = '".$company_row[Province]."'
									
									)		
							
							)
				)
		
		");
		
		
		$user_of_zone = getFirstRow("select * from users where user_id = '$user_id_of_zone'");		
		$user_email = $user_of_zone[user_email];
		
		//if(!$user_email){
			//send to admin instead	
		//	$user_email = getFirstItem("select user_email from users where user_id = 1");
		//}
		
		//$mail_address = $user_email;
				
		//$the_header = "ระบบรายงานผลการจ้างงานคนพิการ: มีการยื่นเอกสารออนไลน์มาจากสถานประกอบการ";
	
		//$the_body = "<table><tr><td>เรียน ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ<br><br>";

		//$the_body .= "มีการส่งข้อมูลเข้ามาจากสถานประกอบการ ".$company_name." <br>";
		//$the_body .= "กรุณา login เข้าระบบเพื่อตรวจสอบข้อมูลที่สถานประกอบการได้ส่งเข้ามา<br><br>";

		//$the_body .= ", ระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
		
		
		//if ($server_ip == "203.146.215.187"){
			//ictmerlin.com use default mail
		//	mail($mail_address, $the_header, $the_body);
		//}elseif ($server_ip == "127.0.0.1"){		
			//donothin			
		//}else{
			//use smtp
			//doSendMail($mail_address, $the_header, $the_body);
		//}

		//yoes 20180113 -- send mail here instead
        $vars = array(

            "{company_name}" => $company_name

        );

        sendMailByEmailId(1, $vars, $company_row[Province]);
		
		
	}
	
	
	mysql_query($sql);
	
	
	
	//also update payment info (if any)
	if($_POST["PaymentMethod"]){
		
		$payment_method = $_POST["PaymentMethod"];
		$ref_no = $_POST[$payment_method."_ref_no"];
		$amount = deleteCommas($_POST["Amount"]);
		
		//$the_pay_date = $_POST["the_pay_date_year"]."-".$_POST["the_pay_date_month"]."-".$_POST["the_pay_date_day"];	//this one is "payment" date
		//$the_note_date = $_POST["the_note_date_year"]."-".$_POST["the_note_date_month"]."-".$_POST["the_note_date_day"];	//this one is "note" date
		//$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];	//this one is cheque date
		
		
		//20140303
		//note date/pay date is the same as payment date		
		$the_pay_date = $_POST["the_pay_date_year"]."-".$_POST["the_pay_date_month"]."-".$_POST["the_pay_date_day"];
		
		
		//yoes 20151122 -- add default value if not select anythin
		if($the_pay_date == "0000-00-00"){
			
			$the_note_date = date("Y")."-".date("m")."-".date("d");
			$the_pay_date = $the_note_date;	
			$the_date = $the_note_date;	
			
		}else{
			$the_note_date = $the_pay_date;
			$the_date = $the_pay_date;	
		}
		
				
		$bank_id = $_POST["check_bank"];
	
		$sql = "
				replace into payment_company(
		
					CID
					, Year
					, PaymentMethod
					, PaymentDate
					, RefNo
					, bank_id
					, Amount
					, PayDate
					, NoteDate
		
				)values(
				
					'$this_id'
					, '$this_year'
					, '$payment_method'
					, '$the_date'
					, '$ref_no'
					, '$bank_id'
					, '$amount'
					, '$the_pay_date'
					, '$the_note_date'
				
				)
				";
				
		//echo $sql;exit();
		mysql_query($sql);
		
	}
	
	
	
	
	//---> handle attached files
	$file_fields = array(
						"company_docfile"
						
						, "company_33_docfile_1"
						, "company_33_docfile_2"
						, "company_33_docfile_3"
						, "company_33_docfile_4"
						, "company_33_docfile_5"
						
						, "company_33_docfile_6"
						, "company_33_docfile_7"
						
						, "company_34_docfile_1"
						
						, "company_35_docfile_1"
						, "company_35_docfile_2"
						, "company_35_docfile_3"					
						
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		
	
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		
		if($hire_docfile_size > 0){
			
			
			
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			
			//echo $hire_docfile_type;
			
			//yoes 20151124 --> only allow some file type only
			//disallow upload for certain file type
			$allowed = array("image/jpeg", "image/jpg", "image/gif", "application/pdf");
			$allow_file_upload = 1;
			if(!in_array($hire_docfile_type, $allowed)) {
			  //$error_message = 'Only jpg, gif, and pdf files are allowed.';
			  //$error = 'yes';
			  $allow_file_upload = 0;
			}
			
			//echo  $allow_file_upload; exit();
				
			
			if($allow_file_upload){
			
			
				//new file name
				$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
				$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
				
				//echo $hire_docfile_path; exit();
				//echo $hire_docfile_path;exit();
				//
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
							,'$lawful_id'
							,'".$file_fields[$i]."'
						)";
				
					mysql_query($sql);
					
				}
			
			}
			
		}else{
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
		
		}
	
	}
	
	//exit();
	
	
	//yoes 20180320
	//also check if have m33 files
	if($_FILES["upload_file_m33"]['size']){
		
		//echo "have m33"; exit();
		
		$file_type = $_FILES["upload_file_m33"]['type'];
		$file_name = $_FILES["upload_file_m33"]['name'];
		
		if($file_type != "application/vnd.ms-excel"){
			
		}else{
			
			$upload_folder = "hire_docfile/";
			
			$file_name_tmp = $_FILES["upload_file_m33"]['tmp_name'];
			$new_file_name = date("ymdhis").rand(00,99)."_".$file_name;
			$file_new_path = $upload_folder.$new_file_name;
			
			move_uploaded_file($file_name_tmp,$file_new_path);
			
			
			define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
			require_once './PHPExcel/Classes/PHPExcel/IOFactory.php';
			
			$objPHPExcel = PHPExcel_IOFactory::load($file_new_path);
			
			
			$data = array();
			$cheque_clear = array();
			$cheque_clear_date = array();
			$cheque_cancel = array();			
			$cheque_cancel_date = array();
			
			$sheet_count = 0;
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				
					$sheet_count++;
					//echo "$sheet_count<br>";
					
					$row_count = 1;
					
					foreach ($worksheet->getRowIterator() as $row) {
				
						$row_count++;
						
						
						if(doCleanInput($worksheet->getCell("B$row_count")->getValue()) && doCleanInput($worksheet->getCell("E$row_count")->getValue())){
							//have value -> do something							
						}else{
							continue;
						}
						
						if(doCleanInput($worksheet->getCell("C$row_count")->getValue()) == "หญิง"){
							$gender = "f";
						}else{
							$gender = "m";
						}
						
						
						$the_date = doCleanInput($worksheet->getCell("G$row_count")->getValue());
						$the_date = (substr($the_date,6,4)-543)."-".substr($the_date,3,2)."-".substr($the_date,0,2);
						
						$sql = "
						
							insert into
								lawful_employees_company(								
									
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
									,le_created_date
									,le_created_by


								
								)values(
								
									'".doCleanInput($worksheet->getCell("B$row_count")->getValue())."'
									,'".$gender."'
									,'".doCleanInput($worksheet->getCell("D$row_count")->getValue())."'
									,'".doCleanInput($worksheet->getCell("E$row_count")->getValue())."'
									,'".doCleanInput($worksheet->getCell("F$row_count")->getValue())."'
									
									,'".$the_date."'
									,'".doCleanInput($worksheet->getCell("H$row_count")->getValue())."'
									,'".doCleanInput($worksheet->getCell("I$row_count")->getValue())."'
									,'$this_year'
									,'$this_id'
									
									,'0'							
									,now()
									,'$sess_userid'
								
								)
						
						";
						
						
						//echo $sql; exit();
						mysql_query($sql);
						
						
						
					}
					
			}
			
			//exit();
			
		}
		
	}
	
	
	
	//also check if have m35 files
	if($_FILES["upload_file_m35"]['size']){
		
		//echo "have m33"; exit();
		
		$file_type = $_FILES["upload_file_m35"]['type'];
		$file_name = $_FILES["upload_file_m35"]['name'];
		
		if($file_type != "application/vnd.ms-excel"){
			
		}else{
			
			$upload_folder = "hire_docfile/";
			
			$file_name_tmp = $_FILES["upload_file_m35"]['tmp_name'];
			$new_file_name = date("ymdhis").rand(00,99)."_".$file_name;
			$file_new_path = $upload_folder.$new_file_name;
			
			move_uploaded_file($file_name_tmp,$file_new_path);
			
			
			define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
			require_once './PHPExcel/Classes/PHPExcel/IOFactory.php';
			
			$objPHPExcel = PHPExcel_IOFactory::load($file_new_path);
			
			
			$data = array();
			$cheque_clear = array();
			$cheque_clear_date = array();
			$cheque_cancel = array();			
			$cheque_cancel_date = array();
			
			$sheet_count = 0;
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				
					$sheet_count++;
					//echo "$sheet_count<br>";
					
					$row_count = 1;
					
					foreach ($worksheet->getRowIterator() as $row) {
				
						$row_count++;
						
						
						if(doCleanInput($worksheet->getCell("B$row_count")->getValue()) && doCleanInput($worksheet->getCell("E$row_count")->getValue())){
							//have value -> do something							
						}else{
							continue;
						}
						
						if(doCleanInput($worksheet->getCell("C$row_count")->getValue()) == "หญิง"){
							$gender = "f";
						}else{
							$gender = "m";
						}
						
						$the_parent = 0;	
						$is_disable = 0;
						
						$the_date = doCleanInput($worksheet->getCell("H$row_count")->getValue());
						$the_date = (substr($the_date,6,4)-543)."-".substr($the_date,3,2)."-".substr($the_date,0,2);
						
						$the_date_2 = doCleanInput($worksheet->getCell("I$row_count")->getValue());
						$the_date_2 = (substr($the_date_2,6,4)-543)."-".substr($the_date_2,3,2)."-".substr($the_date_2,0,2);
						
						$sql = "
						
							insert into
								curator_company(								
									
									curator_name
									,curator_gender
									,curator_age
									,curator_idcard
									,curator_parent
									
									,curator_disable_desc
									
									,curator_start_date
									,curator_end_date
									,curator_event
									,curator_value									
									,curator_lid
															
									,curator_created_date
									,curator_created_by
									, curator_is_disable


								
								)values(
								
									'".doCleanInput($worksheet->getCell("B$row_count")->getValue())."'
									,'".$gender."'
									,'".doCleanInput($worksheet->getCell("D$row_count")->getValue())."'
									,'".doCleanInput($worksheet->getCell("E$row_count")->getValue())."'
									,'$the_parent'
									
									,'".doCleanInput($worksheet->getCell("G$row_count")->getValue())."'
									
									,'".$the_date."'
									,'".$the_date_2."'
									,'".doCleanInput($worksheet->getCell("J$row_count")->getValue())."'
									,'".doCleanInput($worksheet->getCell("K$row_count")->getValue())."'
									,'$lawful_id'
															
									,now()
									,'$sess_userid'
									, '$is_disable'
								
								)
						
						";
						
						
						//echo $sql; exit();
						mysql_query($sql);
						
						
						
					}
					
			}
			
			//exit();
			
		}
		
	}
	
	
		
	if($_POST["auto_post"]){
		header("location: organization.php?id=$this_id&focus=lawful&updated=updated&year=".$_POST["Year"].""); 		
	}else{
		header("location: organization.php?id=$this_id&focus=lawful&updated=updated&year=".$_POST["Year"].""); 	
	}
	
	exit();

?>