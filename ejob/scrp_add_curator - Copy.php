<?php


//20131203
//if this is compnay then put it to curator_company instead
$curator_table_name = "curator";

if($sess_accesslevel == 4){
	$curator_table_name = "curator_company";
}


//check if button is pressed
if($_POST["do_add_curator"]){

	
	//echo "try_add_curator"; exit();

	$curator_id = $_POST["curator_id"]*1;

	$curator_name = doCleanInput($_POST["curator_name"]);
	
	if($_POST["curator_idcard"]){
		$curator_idcard = doCleanInput($_POST["curator_idcard"]);
	}else{
	
		for($i=1;$i<=13;$i++){
			$curator_idcard .= $_POST["id_".$i];
		}
	}
	
	$curator_gender = doCleanInput($_POST["curator_gender"]);
	$curator_age = doCleanInput($_POST["curator_age"]);
	$curator_lid = doCleanInput($_POST["curator_lid"]);
	$curator_parent = doCleanInput($_POST["curator_parent"]);
	
	$curator_event = doCleanInput($_POST["curator_event"]);
	$curator_event_desc = doCleanInput($_POST["curator_event_desc"]);

	$curator_disable_desc = doCleanInput($_POST["le_disable_descconc"]);	
	
	$curator_value = doCleanInput(deleteCommas($_POST["curator_value"]));
	
	$curator_is_disable = doCleanInput($_POST["curator_is_disable"]);	
	
	if($curator_parent > 0){
	
		//if curator has parent then this is disabled curator
		$curator_is_disable = 1;
	
	}
	
	
	$curator_start_date = $_POST["curator_start_date_year"]."-".$_POST["curator_start_date_month"]."-".$_POST["curator_start_date_day"];
	$curator_end_date = $_POST["curator_end_date_year"]."-".$_POST["curator_end_date_month"]."-".$_POST["curator_end_date_day"];


	if($curator_id > 0){
	
		
		//yoes 20150617
		//check if curator existe
		//have post curator id -> do update instead
		$sql = "
			select count(*) from $curator_table_name where 
				
					curator_name = '$curator_name'
					and curator_idcard = '$curator_idcard'
					and curator_gender = '$curator_gender'
					and curator_age = '$curator_age'
					and curator_lid = '$curator_lid'
					and curator_parent = '$curator_parent'
					
					and curator_event = '$curator_event'
					and curator_event_desc = '$curator_event_desc'
					and curator_disable_desc = '$curator_disable_desc'
					
					and curator_is_disable = '$curator_is_disable'
					and curator_start_date = '$curator_start_date'
					and curator_end_date = '$curator_end_date'
					
					and curator_value = '$curator_value'
					
					and curator_id = '$curator_id'
				
			";
			
		
		$row_existed = getFirstItem($sql);
		
		//echo $sql; exit();
		
	
	
		//have post curator id -> do update instead
		$sql = "
			update $curator_table_name set 
				
					curator_name = '$curator_name'
					,curator_idcard = '$curator_idcard'
					,curator_gender = '$curator_gender'
					,curator_age = '$curator_age'
					,curator_lid = '$curator_lid'
					,curator_parent = '$curator_parent'
					
					,curator_event = '$curator_event'
					,curator_event_desc = '$curator_event_desc'
					,curator_disable_desc = '$curator_disable_desc'
					
					, curator_is_disable = '$curator_is_disable'
					, curator_start_date = '$curator_start_date'
					, curator_end_date = '$curator_end_date'
					
					, curator_value = '$curator_value'
					
					, curator_is_dummy_row = 0
					
				where
					
					curator_id = '$curator_id'
				
			";
	
	
	}else{
	
		//no input curator id -> do update
		$sql = "
			insert into 
				$curator_table_name(
				
					curator_name
					,curator_idcard
					,curator_gender
					,curator_age
					,curator_lid
					,curator_parent
					
					,curator_event
					,curator_event_desc
					,curator_disable_desc
					
					, curator_is_disable
					, curator_start_date
					, curator_end_date
					
					, curator_value
					
					, curator_created_date
					, curator_created_by
					
				)values(
				
				
					'$curator_name'
					,'$curator_idcard'
					,'$curator_gender'
					,'$curator_age'
					,'$curator_lid'
					,'$curator_parent'
					
					,'$curator_event'
					,'$curator_event_desc'
					,'$curator_disable_desc'
					
					, '$curator_is_disable'
					, '$curator_start_date'
					, '$curator_end_date'
					
					, '$curator_value'
					
					, now()
					, '".$sess_userid."'
				
				)
				
			";
			
			//yoes 20150617 - check if should put this into stats or not
			$row_existed = 0;
			
	}
	
	//echo $sql; exit();
	
	$curate = "curate";
	
	
	mysql_query($sql) or die(mysql_error());
	
	$inserted_curator_id = mysql_insert_id();
	
	//echo $inserted_curator_id;
	//exit();
	
	if(!$inserted_curator_id){
		$inserted_curator_id = $curator_id;
	}
	
	
	$curator_selected_company = getFirstItem("select CID from lawfulness where LID = '$curator_lid'");

	//yoes 20150617 - only add to history if curator data is changed	
	if(!$row_existed){
		//then add this to history
		//$history_sql = "insert into modify_history values('$sess_userid','$curator_selected_company',now(),8)";
		//mysql_query($history_sql);
		doAddModifyHistory($sess_userid,$curator_selected_company,8,$curator_lid);		
		//also add fulllog
		doCuratorFullLog($sess_userid, $inserted_curator_id, basename($_SERVER["SCRIPT_FILENAME"]), 0);	
		
		//yoes 20160208
		resetLawfulnessByLID($curator_lid);
		
	}
	
	//end add curator
	
	//echo $this_year; exit();
	//if($this_year >= 2013 && $sess_accesslevel != 4){
	
	
	//yoes 20151222 -- now allow all years to do an auto-post
	if($sess_accesslevel != 4){
	
		//only do auto post if >= year 2013
		$_GET["auto_post"] = 1;
		
		//also add curator flag
		$_GET["curate"] = "curate";
		
	}


	//$inserted_id = mysql_insert_id();
	
	
	
	$updated_done = 1;
	
	if($inserted_id && $_GET["auto_post"]){
		$_GET["curator_id"] = $inserted_id;
	}else{
		//$_GET["curator_id"] = $curator_id;
	}

	///
	//---> handle attached files
	$file_fields = array(
						"curator_docfile"
						,"curator_docfile_2"
						,"curator_docfile_3"
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
						,'$inserted_curator_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
			
			
			
		
		}
	
	}		
	
	
	
	//redirect page?
	//http://localhost/hire_projects/view_curator.php?curator_id=6
	/* yoes 20160120 ---> just no longer use view_curator.php page
	if($curator_is_disable == 0){
		
		if($sess_accesslevel == 4){
			header("location: view_curator.php?curator_id=$inserted_curator_id");
		}elseif($_POST[case_closed]){			
			header("location: view_curator.php?curator_id=$inserted_curator_id"."&extra=extra");
		}else{			
			//also do auto-post for non-company users
			header("location: view_curator.php?curator_id=$inserted_curator_id"."&do_auto_post=1");
		}
		
		
	}
	*/
	
	
	//yoes 20160120 --> also add curator usee here...
	if($inserted_curator_id){
		$curator_parent_id = $inserted_curator_id;
	}elseif($curator_id){
		$curator_parent_id = $curator_id;
	}
	
	if(!$curator_is_disable){
		
		//if this curator is "new" and "not disabled"
		//try insert or update usee
		if($_POST["usee_curator_id"]){
			$usee_curator_id = 	getFirstItem("
								
								select 
									curator_id
								from 
									$curator_table_name 
								where 
									curator_id = '". doCleanInput($_POST["usee_curator_id"]) . "'
									and 
									curator_parent != 0
									and
									curator_is_disable = 1
									
								");
		}
		
		
		for($i=1;$i<=13;$i++){
			$useeid_card .= $_POST["useeid_".$i];
		}

		if($usee_curator_id){

			$sql = "
				update $curator_table_name set 
					
						curator_name = '".doCleanInput($_POST["usee_name"])."'
						,curator_idcard = '".$useeid_card."'
						,curator_gender = '".doCleanInput($_POST["usee_gender"])."'
						,curator_age = '".doCleanInput($_POST["usee_age"])."'
						,curator_lid = '$curator_lid'
						
						,curator_disable_desc = '".doCleanInput($_POST["le_disable_descusee"])."'
						
						, curator_is_dummy_row = 0
						
					where
						
						curator_id = '".doCleanInput($_POST["usee_curator_id"])."'
					
				";
			
		}else{
		
			$sql = "
				insert into 
					$curator_table_name(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						
						,curator_parent					
						,curator_event
						,curator_event_desc
						,curator_disable_desc					
						, curator_is_disable
						
						, curator_start_date
						, curator_end_date					
						, curator_value
						
						, curator_created_date
						, curator_created_by
					
						
					)values(
					
					
						'".doCleanInput($_POST["usee_name"])."'
						,'".$useeid_card."'
						,'".doCleanInput($_POST["usee_gender"])."'
						,'".doCleanInput($_POST["usee_age"])."'
						,'$curator_lid'
						
						,'$curator_parent_id'					
						,''
						,''
						,'".doCleanInput($_POST["le_disable_descusee"])."'					
						, '1'
						
						, ''
						, ''					
						, ''
						
						, now()
						, '".$sess_userid."'
					
					)
					
				";
			
		}
			
		mysql_query($sql);
		
		//add full log
		//mysql_query($history_sql);
		doAddModifyHistory($sess_userid,$curator_selected_company,8,$curator_lid);		
		//also add fulllog
		doCuratorFullLog($sess_userid, $inserted_curator_id, basename($_SERVER["SCRIPT_FILENAME"]), 1);	
		
		//yoes 20160208
		resetLawfulnessByLID($curator_lid);
			
			
		
	}else{
		
		//also add log before do a delete
		doCuratorFullLog($sess_userid, $inserted_curator_id, basename($_SERVER["SCRIPT_FILENAME"]), 1);	
		
		
		
		//curator is disabled -> delete all child
		$sql = "
			delete 
			from 
				$curator_table_name
			where
				curator_parent = '$curator_parent_id'
			
			";
			
		mysql_query($sql);
		
		//yoes 20160208
		resetLawfulnessByLID($curator_lid);
		
		
		
	}
	//


}//end insert curataor

//yoes 20160402 -- do double-redirect for company
if($curate){
	header("location: organization.php?id=".$this_cid."&focus=lawful");
}

?>