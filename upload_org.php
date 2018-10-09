<?php

	include "db_connect.php";
	include "session_handler.php";
	
	error_reporting(1);
	ini_set('max_execution_time', 1800);
	ini_set("memory_limit","256M");
	
	//variables
	$upload_folder = "./to_import/";
	$this_lawful_year = 2017;
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_org_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_org_new.php");
		exit();	
	}

	$files = glob($upload_folder . '*.txt');

	foreach ($files as $filename) {
		$import_filename = $filename;	
	}
	
	
	if(!$import_filename){
		doUploadOrgLog($sess_userid, "ไม่พบไฟล์ที่จะทำการ import เข้า temp table", $import_filename);	
		header("location: import_org_new.php?nofile=1");
		exit();
	}
	//echo $import_filename;
	//exit();
	
	$file=$import_filename;
	$linecount = 0;
	$handle = fopen($file, "r");
	
	
	$files = glob($upload_folder . '*.zip');
					
	foreach ($files as $filename) {
		$old_zip_file = $filename;			
	}
	
	//file is ok -> delete current temp table to prepare file import
	  $sql = "truncate table company_temp_all";
	  mysql_query($sql) or die(mysql_error());
	   doUploadOrgLog($sess_userid, "เตรียมการนำเข้าข้อมูล", str_replace($upload_folder,"",$old_zip_file));	
	
	
	
	//start doing import file -> mark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_org_file'
			, DATE_ADD(NOW(), INTERVAL 30 minute)
		)
		
	";
	
	mysql_query($sql) or die(mysql_error());
	
	/**/
	//while(!feof($handle) && $lineall <= 500000){
	//while(!feof($handle) && $lineall <= 50){
	while(!feof($handle)){
	 // $line = utf8_encode(fgets($handle));
	 // $line = to_utf(fgets($handle));
	  $line = fgets($handle);
	  $linecount++;
	  $lineall++;
	  
	  
	  if(strlen(trim($line)) > 0 ){
	  //for each line -> try echo it out
	  //echo "<br>$line";
	  
			$data .= '"'. doCleanInput(to_utf(trim(substr($line, 0, 10)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 10, 6)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 16, 2)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 18, 50)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 68, 30)))) . '"';
			
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 98, 30)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 128, 20)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 148, 20)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 168, 5)))) . '"';
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 173, 10)))) . '"';
			
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 183, 4)))) . '"';		
			$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 187, 5)))) . '"';
			
			$data .= "\r\n";
		
	  }
	 
	  
	}
	
	
	
	fclose($handle);
	
	file_put_contents($upload_folder."temp/nay301-mini-2.txt", $data);
	
	
	//
	$mm = mysql_query("
	
		LOAD DATA LOCAL INFILE '".$upload_folder."temp/nay301-mini-2.txt' 
		INTO TABLE company_temp_all
			CHARACTER SET UTF8
			FIELDS TERMINATED BY ',' 
			OPTIONALLY ENCLOSED BY '".'"'."'
			LINES TERMINATED BY '\r\n'
			
		
		");
		
		//echo "woot";
		//exit();
	if($mm){
		 doUploadOrgLog($sess_userid, "นำไฟล์เข้า เพื่อทำการตรวจสอบสำเร็จ", str_replace($upload_folder,"",$old_zip_file));	
	}else{
	   doUploadOrgLog($sess_userid, "นำไฟล์เข้าทำการตรวจสอบไม่สำเร็จ", str_replace($upload_folder,"",$old_zip_file));	
	   header("location: import_org_new.php?failedsql=1");
		exit();
	}
		
	
	
	//yoes 20160205 --- first thing first....
	//update สุราษฏร์ธานี to สุราษฎร์ธานี
	$sql = "
	
			update									
				company_temp_all
			set
				province = 'สุราษฎร์ธานี'
			where
				
				province = 'สุราษฏร์ธานี'
				
	
	
	";
	
	//echo $sql; exit();
	
	mysql_query($sql);
	
	
		
	//yoes 20160203 --- add error rows
	$sql = "
	
			update									
				company_temp_all
			set
				is_error = 1
			where
				
				char_length(CompanyCode) != 10
				or
				CompanyCode REGEXP '[^0-9]+$'
				
				or
				char_length(BranchCode) != 6
				or
				BranchCode REGEXP '[^0-9]+$'
													
				or
				
				CompanyTypeCode not in (									
					select
						CompanyTypeCode
					from
						companytype									
				)
				
				or 
				trim(CompanyNameThai) = ''
				or 
				CompanyNameThai is null
				
				
				or
				
				Province not in (									
					select
						province_name
					from
						provinces									
				)
				
				
				
				or
				
				BusinessTypeCode not in (									
					select
						BusinessTypeCode
					from
						businesstype									
				)
				
				
				
				or 
				trim(Employees) = ''
				or 
				Employees is null
				
	
	
	";
	
	mysql_query($sql);
	
	
	//delete not-in-case company
	// 148 seconds to run this
	$sql = "
	
		update
			company_temp_all
		set
			is_in_case = 1
		where
			companyCode in (
				
				
				select companyCode from (
					select
						companyCode
						, sum(employees)
					from
						company_temp_all
					group by
						companyCode
					having 
						sum(employees) >= 100
				) mnm
					
			)
	
	";
	
	mysql_query($sql);
	
	echo "done delete not in case";
	
	
	//yoes 20150205 --- set government org into "not in case"
	$sql = "
	
		update
			company_temp_all
		set
			is_in_case = 0
			, is_government = 1
		where
			CompanyTypeCode = 14
			or
			companyCode in (
				
				
				select companyCode from (
					select
						companyCode
					from
						company
					where
						companyCode = 299
				) mnm
					
			)
	
	";
	
	mysql_query($sql);
	
	echo "done delete not in case";
	
	
	
	
	//yoes 20160203 ---> also mark company that already have lawfulness records
	$sql = "
	
		update
			company_temp_all a
				join 
				
				(
					
					
					select 
						companyCode 
					from 
						company a
							join
							lawfulness b
								on a.cid = b.cid
								and b.year = $this_lawful_year
								and
								b.LawfulStatus != 0
						
				) bbbb
				on a.companyCode = bbbb.companyCode
				and
				a.is_in_case = 1
		set
			a.has_lawfulness = 1
		
	
	";
	
	mysql_query($sql) or die(mysql_error());
	
	
	//yoes 20160203 ---> also mark company that have no main branch
	$sql = "
	
		update
			company_temp_all	
		set
			no_main_branch = 1
		where
			is_in_case = 1			
								
			and
			branchcode > 1
			and
			companycode not in (
				
				select companycode from (
					select companycode
					FROM `company_temp_all` 
					where 
					branchcode < 1
					and
					is_in_case = 1				
				) bbb
			)			
	
	";
	
	mysql_query($sql) or die(mysql_error());
	
	
	//start doing import file -> unmark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_org_file'
			, NOW()
		)
		
	";
	
	mysql_query($sql) or die(mysql_error());
	
	header("location: import_org_new.php");
?>