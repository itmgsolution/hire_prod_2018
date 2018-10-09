<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	
	
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	
	
	$table_name = "invoices";
	
	
	//
	$invoice_cid = $_POST["invoice_cid"]*1;
	$invoice_lawful_year = $_POST["invoice_lawful_year"];
	//$invoice_payment_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];			
	$invoice_payment_date = $_POST["invoice_payment_date"];			
	$invoice_date = date("Y-m-d");
	$invoice_amount = round(deleteCommas($_POST['invoice_amount']),2)*1;
	
	//amounts and remarks
	
	$invoice_principal_amount = round($_POST['invoice_principal_amount'],2)*1;
	$invoice_interest_amount = round($_POST['invoice_interest_amount'],2)*1;
	$invoice_remarks = $_POST['invoice_remarks'];
	$invoice_userid = $_POST['invoice_userid'];
	
	$invoice_owned_principal = round($_POST['invoice_owned_principal'],2);
	$invoice_owned_interest = round($_POST['invoice_owned_interest'],2);
	
	$special_fields = array(
						
						"invoice_cid"
						, "invoice_lawful_year"
						, "invoice_payment_date"
						, "invoice_date"
						, 'invoice_amount'
						
						, 'invoice_principal_amount'
						, 'invoice_interest_amount'
						, 'invoice_remarks'
						, 'invoice_userid'
						, 'invoice_userid_text'
						
						, 'invoice_owned_principal'
						, 'invoice_owned_interest'
						
						, 'invoice_status'
						
						);
	
	
	$special_values = array(
	
	
						"'$invoice_cid'"
						, "'$invoice_lawful_year'"
						, "'$invoice_payment_date'"
						, "'$invoice_date'"
						, "'".deleteCommas($_POST['invoice_amount'])."'"
						
						, "'$invoice_principal_amount'"
						, "'$invoice_interest_amount'"
						, "'$invoice_remarks'"
						, "'$invoice_userid'"						
						, "'". getFirstItem("select CONCAT(FirstName, ' ', LastName) from users where user_id = '$invoice_userid'") ."'"
						
						, "$invoice_owned_principal"
						, "$invoice_owned_interest"
						
						, '1'
	
						);
						
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	$this_invoice_id = mysql_insert_id();					
	
	
	//---> handle attached files
	$file_fields = array(
						"invoice_docfile"
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
						,'$this_invoice_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
		
		}
	
	}
	
	
	
		
	//header("location: add_invoice.php?search_id=".$invoice_cid."&mode=payment&for_year=".$invoice_lawful_year );
	header("location: invoice.php?invoice_id=".$this_invoice_id );
	exit();
		
	

?>