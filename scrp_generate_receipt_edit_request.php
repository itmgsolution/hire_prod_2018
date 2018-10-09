<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	
	
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	
	
	
	
	
	//
	
	$edit_date = date("Y-m-d");;
	
	
	//amounts and remarks
	
	$edit_rid = $_POST['edit_rid']*1;
	//$cancel_pid = $_POST['cancel_pid']*1;
	
	$edit_reason = $_POST['edit_reason'];
	$edit_userid = $_POST['edit_userid'];
				
				
	$selected_year = $_POST["ddl_year"];
	/*
	$sql = "
		
		insert into 
			receipt_edit_requests
		select
			*
			, '$edit_reason'
			, '$edit_userid'
			, '$edit_date'
			, 0
		from
			receipt	
		where
			rid = '$edit_rid'
	
	";*/
	
	
	//yoes 20170116 -- for CANCEL
	if($_POST[do_request_cancel_receipt]){
		
		$_POST["Amount"] = 0;
		
	}
	
	//print_r($_POST); exit();
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	$table_name = "receipt_edit_request";

	$input_fields = array(
						'PaymentMethod'
						
						,'ReceiptNote'
						
						);
	
	
	
	//yoes 20170115 -- add payment methods
	$payment_method = $_POST["PaymentMethod"];
	$ref_no = $_POST[$payment_method."_ref_no"];
	if($payment_method == "Cheque"){
		$bank_id = $_POST["check_bank"];
	}
	
	
	
	$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];			
							
	$special_fields = array("ReceiptYear","ReceiptDate",'Amount'
	
							, 'edit_reason'
							, 'edit_userid'
							, 'edit_date'
							, 'edit_status'
							
							, 'rid'
							
							, 'RefNo'
							, 'bank_id'
							
							);
	$special_values = array("'$selected_year'" ,"'$the_date'" ,"'".deleteCommas($_POST["Amount"])."'"
	
							, "'$edit_reason'"
							, "'$edit_userid'"
							, "now()"
							, "'0'"
							, "'$edit_rid'"
	
							, "'$ref_no'"
							, "'$bank_id'"
							
							);
						
	//$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values," where RID = '$this_id'");
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	
	//---> handle attached files
	$file_fields = array(
						"edit_docfile"
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		//echo "filesize: ".$hire_docfile_size;
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
			//echo $hire_docfile_path;
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
						,'$this_cancel_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
		
		}
	
	}
	
	
	
		
	header("location: view_payment.php?id=".$edit_rid );
	exit();
		
	

?>