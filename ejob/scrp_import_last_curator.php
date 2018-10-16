<?php

	include "db_connect.php";
	
	//table name
	
	$this_cid = doCleanInput($_POST["le_cid"]);
	$this_year = doCleanInput($_POST["le_year"]);
	$last_year = $this_year - 1;
	
	$this_lid = getFirstItem("
	
		select
			lid
		from
			lawfulness_company
		where
			cid = '$this_cid'
			and
			year = '$this_year'
	
	
	");
	
	
	$last_lid = getFirstItem("
	
		select
			lid
		from
			lawfulness_company
		where
			cid = '$this_cid'
			and
			year = '$last_year'	
	
	");
	
	
	//echo $last_lid; exit();
	
	
	//yoes 20140910 -- add le_wage_unit and le_from_oracle
	$the_sql = "insert into curator_company(
							
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
							
							,curator_created_date
							,curator_created_by
							
							, curator_is_dummy_row

							
						) select 
							
							curator_name
							,curator_idcard
							,curator_gender
							,curator_age
							,'$this_lid'
							,curator_parent
							,curator_event
							,curator_event_desc
							,curator_disable_desc
							,curator_is_disable
							,curator_start_date
							,curator_end_date
							,curator_value
							
							, now()
							, '$sess_userid'
							
							, curator_id
							
						 from curator_company 
						where curator_lid = '$last_lid'
					 ";
					 
	
	mysql_query($the_sql);
	
	
	//yoes 20180320
	//reconcile parent lid
		
	$sql = "
	
		update
			curator_company a
				join 
					curator_company b
						on
						a.curator_parent = b.curator_is_dummy_row
		set
			a.curator_parent = b.curator_id
		where
			a.curator_lid = '$this_lid'
			and
			b.curator_lid = '$this_lid'
			and
			a.curator_parent > 0
	
	";
	
	//echo $sql;exit();
	mysql_query($sql);
	
	
	
	
	
	//yoes 20160907 
	//also update hire_numofemp and lawful status
	//$this_lid = getFirstItem("select * from lawfulness where cid = '$this_cid' and year = '$this_year'");
	//resetLawfulnessByLID($this_lid);
	//
	
	header("location: organization.php?id=$this_cid&le=le&focus=lawful&year=".$_POST["le_year"]);

?>