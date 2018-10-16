<?php

	include "db_connect.php";
	
	
	if($_POST["LID"]){
		
		$this_id = $_POST["LID"]*1;
		$this_employess = deleteCommas($_POST["update_employees"])*1;
		
		$this_cid = doCleanInput($_POST["CID"]);
		$this_year = doCleanInput($_POST["this_year"]);
		
	}else{
		exit();
	}
	
	//table name
	
	//echo $this_employess; exit();
	
	//yoes 20160104 -- do a full "change log" here
	doLawfulnessFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]));
	
	//yoes 20160208
	resetLawfulnessByLID($this_id);
	
	//yoes 20160104 -- do a full "change log" here
	
	//yoes 20150617
	//check first if this is "a change"
	$the_sql = "	
				select 		
					count(*)
				from
					lawfulness
				where
					Employees = '$this_employess'
					and 
					LID = '$this_id'				
				";
				
	$row_existed = getFirstItem($the_sql);
				
	
	$the_sql = "	
				update 				
					lawfulness
				set
					Employees = '$this_employess'
				where 
					LID = '$this_id'				
				";
				
				
	$autopost = 1;			
	
	if($sess_accesslevel == 4){
	
		//use company table instead	
		/*$the_sql = "	
				replace into 				
					lawfulness_company(	
						LID			
						,Employees
					)values(
						'$this_id'	
						 ,'$this_employess'					
					)					 
							
				";*/
				
		$the_sql = "	
				update 				
					lawfulness_company
				set
					Employees = '$this_employess'
				where 
					LID = '$this_id'				
				";
		
		$autopost = 0; //dont auto post for company
		
	}
				
				
				
	//echo $the_sql; exit();
	mysql_query($the_sql) or die (mysql_error());
	
	
	//yoes 20150617 - only add history if this is a "change"
	if(!$row_existed){
		//then add this to history
		//$history_sql = "insert into modify_history values('$sess_userid','$this_cid',now(),4)";
		//mysql_query($history_sql);
		doAddModifyHistory($sess_userid,$this_cid,4,$this_id);
		
		//yoes 20160208
		resetLawfulnessByLID($this_id);
	}
				
	
	if(is_numeric($this_cid)){
		
		
		//yoes 20151208
		
		if($_POST[employees_popup_focus] == "dummy"){
			header("location: organization.php?id=$this_cid&focus=dummy&year=$this_year&auto_post=$autopost");
		}else{
			header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&auto_post=$autopost");
		}
		
		
	}else{
		header("location: org_list.php");
	}

?>