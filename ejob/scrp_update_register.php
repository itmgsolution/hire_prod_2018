<?php

	
	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//table name
	$table_name = "users";
	$this_id = doCleanInput($_POST["user_id"]);
	
	//echo $_POST["register_id"] ; exit();
	
	//print_r($_POST); exit();
	
	//echo $sess_userid; exit();

	
	if($this_id == "new"){
		
		$mode = "new";
		
	}else{
	
		
		if($this_id == $sess_userid){
			
			//self-fix
			$is_owner = 1;
			
		}elseif($this_id != $sess_userid && $sess_accesslevel != 1){
			//not "new", not "admin" and "not self-fix"
			header("location: index.php?e=session");
			exit();
		}
		
	}
	
	
		
	if($mode == "new"){
	
		
	
		//insert new, check if user name already existed...
		$user_count = getFirstItem("select count(user_name) from users where user_name = '".doCleanInput($_POST["register_name"])."'");
		
		//yoes 20151102 -- also check for duped email
		$email_count = getFirstItem("select count(user_email) from users where user_email = '".doCleanInput($_POST["register_email"])."' and user_meta != '".doCleanInput($_POST["register_cid"])."'");
		
		
		if($user_count > 0){			
			//redirect back and exit
			header("location: view_register.php?mode=add&duped=duped");
			exit();
		}elseif($email_count > 0){			
			//redirect back and exit
			header("location: view_register.php?mode=add&mailed=mailed");
			exit();
		}else{			
			//continue doing w/e
			
			//specify all posts fields
			$input_fields = array(
	
						'register_name'
						,'register_password'
						
						,'register_org_code'
						,'register_org_name'
						,'register_contact_name'
						,'register_contact_phone'
						,'register_position'
						,'register_email'
						
						);
			
			$special_fields = array(	
									'register_province'		
									, 'register_registered_date'		 //yoes 20151019		
							); 
			$special_values = array(	
									"'".$_POST["Province"]."'"	
									, "now()"				//yoes 20151019	
							); 	
			
			
			
			//$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);				
			
			//yoes 20151019	- add created DATE
			
			$the_sql = "insert into users(
						
							user_name
							, user_password
							, AccessLevel
							, FirstName
							, LastName
							, user_meta
							
							, user_enabled
							
							, user_email
							, user_position
							, user_telephone
							
							, user_created_date
							, user_commercial_code
							
							, user_ip_address
							
						)values(
						
						
							'".doCleanInput($_POST["register_name"])."'
							, '".doCleanInput($_POST["register_password"])."'
							, '4'
							, '".doCleanInput($_POST["register_contact_name"])."'
							, '".doCleanInput($_POST["register_contact_lastname"])."'
							, '".doCleanInput($_POST["register_cid"])."'
						
							, 9
							
							, '".doCleanInput($_POST["register_email"])."'
							, '".doCleanInput($_POST["register_position"])."'
							, '".doCleanInput($_POST["register_contact_phone"])."'
							
							, now()
							, '".doCleanInput($_POST["user_commercial_code"])."'
							, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
							
						)";
			
			
			//echo $the_sql; exit();
			mysql_query($the_sql);	
			$this_id = mysql_insert_id();
			
			//yoes 20141013 --> also send out emails
			//yoes 20151102 --> change wording for emails
			$mail_address = doCleanInput($_POST["register_email"]);
			
			
			//
			$this_id = $this_id;
			$this_register_name = $_POST["register_name"];
			$this_seed = $this_id+doCleanInput($_POST["register_cid"])+7890;
			$this_cid = $_POST["register_cid"];
			
			include "template_register_email.php";
			
			
			doSendMail($mail_address, $the_header, $the_body);	
			
			
			
			//also update register stat
			$history_sql = "insert into modify_history_register(mod_register_id, mod_date, mod_type) values('$this_id',now(),1)";
			mysql_query($history_sql);
			
			
			
			
			
			
			
			//yoes20141106 --> also add file attachment
			//---> handle attached files
			$file_fields = array(
			
								"register_employee_card"
								,"register_id_card"
								
								
								);
								
			for($i = 0; $i < count($file_fields); $i++){
			
				$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
				
				if($hire_docfile_size > 0){
					
					$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
					$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
					$hire_docfile_exploded = explode(".", $hire_docfile_name);
					$hire_docfile_file_name = $hire_docfile_exploded[0]; 
					$hire_docfile_extension = $hire_docfile_exploded[1]; 
					
					
					//echo $hire_docfile_type; exit();
					
					if($hire_docfile_type == "image/jpeg" || $hire_docfile_type == "image/gif"
						|| $hire_docfile_type == "image/png" || $hire_docfile_type == "application/pdf"
					
					){
					
						//new file name
						$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
						$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
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
									,'$this_id'
									,'".$file_fields[$i]."'
								)";
						
							mysql_query($sql);
							
						}
						
					}else{
					
						//yoes 20160816 -- do nothing	
						
					}
					
					
				}else{
					
					//no new file uploaded, retain old file name in db
					//array_push($special_fields,$file_fields[$i]);
					//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
				
				}
			
			}
			///
			//end handle attached file
			//////
			
			
			
			
			
			
			
			header("location: view_register.php?id=$this_id&user_added=user_added");
		}
		
	}elseif($sess_accesslevel == 1 || $is_owner){//edit mode
	
		//edit other people (only admin can do this)
	
		//yoes 20160402 -- rearrange values
		$_POST['FirstName'] = $_POST['register_contact_name'];
		$_POST['LastName'] = $_POST['register_contact_lastname'];
		$_POST['user_telephone'] = $_POST['register_contact_phone'];
		$_POST['user_position'] = $_POST['register_position'];
		$_POST['user_password'] = $_POST['register_password'];
		
		
		//more items 20160425
		$_POST['FirstName_2'] = $_POST['register_contact_name_2'];
		$_POST['LastName_2'] = $_POST['register_contact_lastname_2'];
		$_POST['user_telephone_2'] = $_POST['register_contact_phone_2'];
		$_POST['user_position_2'] = $_POST['register_position_2'];
		
		
		//yoes 20160506
		//add this?
		/*
		$input_fields = array(
			'user_password'				
			);*/
		
		
		
		$special_fields = array(); 
		$special_values = array(); 	
		
		
		if($_POST["user_password"]){
			array_push($special_fields, 'user_password');
			array_push($special_values, "'".doCleanInput($_POST["user_password"])."'");
		}
		
		
		//yoes 20160404 -- do all these as special fiel
		if($_POST["FirstName"]){
			array_push($special_fields, 'FirstName');
			array_push($special_values, "'".doCleanInput($_POST["FirstName"])."'");
		}
		if($_POST["LastName"]){
			array_push($special_fields, 'LastName');
			array_push($special_values, "'".doCleanInput($_POST["LastName"])."'");
		}
		if($_POST["user_telephone"]){
			array_push($special_fields, 'user_telephone');
			array_push($special_values, "'".doCleanInput($_POST["user_telephone"])."'");
		}
		if($_POST["user_position"]){
			array_push($special_fields, 'user_position');
			array_push($special_values, "'".doCleanInput($_POST["user_position"])."'");
		}
		
		
		//yoes 20160425 - more items
		if($_POST["FirstName_2"]){
			array_push($special_fields, 'FirstName_2');
			array_push($special_values, "'".doCleanInput($_POST["FirstName_2"])."'");
		}
		if($_POST["LastName_2"]){
			array_push($special_fields, 'LastName_2');
			array_push($special_values, "'".doCleanInput($_POST["LastName_2"])."'");
		}
		if($_POST["user_telephone_2"]){
			array_push($special_fields, 'user_telephone_2');
			array_push($special_values, "'".doCleanInput($_POST["user_telephone_2"])."'");
		}
		if($_POST["user_position_2"]){
			array_push($special_fields, 'user_position_2');
			array_push($special_values, "'".doCleanInput($_POST["user_position_2"])."'");
		}						
			
			
		//yoes 20160404 - check user_enabled
		//if it's 9 then change to
		$my_enabled = getFirstItem("select user_enabled from users where user_id = '$this_id' ");
		if($my_enabled == 9){
			array_push($special_fields, 'user_enabled');
			array_push($special_values, "'0'");
		}
		
	
		//print_r($_POST); exit();
		//echo $the_sql; exit();
	
		$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, " where user_id = '$this_id'");
		
		//echo $the_sql; exit();
		
		//also update register stat
		$history_sql = "insert into modify_history_register(mod_register_id, mod_date, mod_type, mod_desc) values('$this_id',now(),6,'$sess_userid')";
		mysql_query($history_sql);
		
		
		
		
		//yoes20141106 --> also add file attachment
			//---> handle attached files
			$file_fields = array(
			
								"register_employee_card"
								,"register_id_card"
								, "register_doc_1"
								, "register_doc_22"
								, "register_doc_2"
								
								, "register_doc_3"
								, "register_doc_4"
								, "register_company_card"
								
								
								);
								
			for($i = 0; $i < count($file_fields); $i++){
			
				$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
				
				if($hire_docfile_size > 0){
					
					$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
					$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
					$hire_docfile_exploded = explode(".", $hire_docfile_name);
					$hire_docfile_file_name = $hire_docfile_exploded[0]; 
					$hire_docfile_extension = $hire_docfile_exploded[1]; 
					
					//new file name
					$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
					$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
					//echo $hire_docfile_path;exit();
					//
					
					//echo $hire_docfile_type; exit();
					
					if($hire_docfile_type == "image/jpeg" || $hire_docfile_type == "image/gif"
						|| $hire_docfile_type == "image/png" || $hire_docfile_type == "application/pdf"
					
					){
						
						//echo $hire_docfile_type; exit();
					
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
									,'$this_id'
									,'".$file_fields[$i]."'
								)";
						
							mysql_query($sql);
							
						}
						
					}else{
												
						//yoes 20160816 -> do nothin
						//echo "what"; exit();
						
					}
					
				}else{
					
					//no new file uploaded, retain old file name in db
					//array_push($special_fields,$file_fields[$i]);
					//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
				
				}
			
			}
			
			
		
		
		mysql_query($the_sql);


        $the_cid = getFirstItem("select user_meta from users where user_id = '".$this_id."'");
        $the_company_name = getFirstItem("select CompanyNameThai from company where cid = '".$the_cid."'");
        $the_company_code = getFirstItem("select CompanyCode from company where cid = '".$the_cid."'");
        $the_company_province = getFirstItem("select Province from company where cid = '".$the_cid."'");

		//yoes 20170124
		//also send mail to admin
		
		//$the_header = "มีการส่งข้อมูลผู้ใช้งานเข้ามาจากผู้ใช้งานสถานประกอบการ";
		
		
		//$the_body = "<table><tr><td>เรียน ผู้ดูแลระบบจ้างงานคนพิการ<br>";

		//$the_body .= "มีการส่งข้อมูลผู้ใช้งานเข้ามาจากระบบ e-service<br>";
		//$the_body .= "สถานประกอบการ: $the_company_name<br>";
		//$the_body .= "เลขที่บัญชีนายจ้าง: $the_company_code<br><br>";
		
		//$the_body .= "กรุณาเข้าสู่ระบบ เพื่อทำการพิจารณาอนุมัติผู้ใช้งานของสถานประกอบการนี้ หลังจากที่สถานประกอบการส่งเอกสารหลักฐานยืนยันตัวตนฉบับจริงเข้ามา";
		
		
		//$mail_address = "p.daruthep@gmail.com";
		//doSendMail($mail_address, $the_header, $the_body);


        //yoes 20180113 -- send mail here instead
        $vars = array(
            "{company_name}" => $the_company_name
            ,"{company_code}" => $the_company_code
        );

        sendMailByEmailId(2, $vars, $the_company_province);
		
		
		header("location: view_register.php?id=$this_id&updated=updated");
		
	}
	
	

?>