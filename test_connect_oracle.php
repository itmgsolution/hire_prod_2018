<?php

	//echo "try connecting oracle server for HIRE PROJECT...";
	
	$user = 'nepfund'; //nep_card
	$password = "fundnep";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 203.146.215.191)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = nepcap)
				)
			)";
			
			
	
			
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");
	
	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		echo "<br><font color='red'>connection failed!</font>";
		exit;
	}

?>...