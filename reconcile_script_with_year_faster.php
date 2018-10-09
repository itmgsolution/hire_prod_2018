<?php 

	include "db_connect.php";
	
	$sql = "
		select 
			cid
			, year 
			, lid
		from 
			company_to_reconcile_with_year 
		where 
			reconciled = 0 
		limit 
			0, 15000
		";
	
	//$reconcile_row = array();
	
	//$reconcile_row = getFirstRow($sql);
	
	//print_r($reconcile_row);
	
	$reconcile_result = mysql_query($sql);
	
	while($reconcile_row = mysql_fetch_array($reconcile_result)){
	
		$cid_to_reconcile = $reconcile_row["cid"];
		$lid_to_reconcile = $reconcile_row["lid"];
		$year_to_reconcile = $reconcile_row["year"];
		
		//echo "<br>cid to reconcile: " . $cid_to_reconcile . " - ";
		//echo "year to reconcile: " . $year_to_reconcile . " - ";
		echo "<br>lid to reconcile: " . $lid_to_reconcile . " - ";
		echo "cid: " . $cid_to_reconcile . " - ";
		
		resetLawfulnessByLID($lid_to_reconcile);
		doLawfulnessFullLog(1, $lid_to_reconcile, "reconcile_script");
		
		mysql_query("update company_to_reconcile_with_year set reconciled = 1 where lid = '".$lid_to_reconcile."'");
		
	}


	if(!$cid_to_reconcile){echo "all company reconciled successfully"; exit();}


	

?>

