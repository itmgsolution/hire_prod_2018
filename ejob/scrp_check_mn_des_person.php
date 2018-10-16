<?php


	if($_SERVER['SERVER_ADDR'] == "127.0.0.1" || $_SERVER['SERVER_ADDR'] == "203.146.215.187"){
		
		$have_record_in_oracle = 1;
		
	}else{
	
		
		/*$user = 'opp$_dba'; //nep_card
		$password = "password";	//password
		$db = "(DESCRIPTION =
					(ADDRESS_LIST = 
						(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.13)(PORT = 1521))
					)
					(CONNECT_DATA =
						(SERVICE_NAME = ORCL)
					)
				)";
		$connect = oci_connect($user, $password, $db, "TH8TISASCII");
		
		$s = oci_parse($connect, "select * from MN_DES_PERSON where PERSON_CODE = '$le_id'");
		oci_execute($s, OCI_DEFAULT);
		$have_record_in_oracle = 0;	
		while (oci_fetch($s)) {
			$have_record_in_oracle = 1;	
		}*/
		
		
		//yoes 20150923 -> use webservice instead
		$url = "http://61.19.50.29/ws/wsjson?user=test&password=test123&queryCode=HIRE01&CARD_ID=$le_id";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 4);
		$json = curl_exec($ch);
		if(!$json) {
			echo curl_error($ch);
		}
		curl_close($ch);
		
		
		//
		$moomin_array = json_decode($json,true);
		
		//print_r($moomin_array["rows"]); exit();
		
		$output_array = $moomin_array["rows"][0];
			
		if($output_array[FIRST_NAME_THAI]){
			$have_record_in_oracle = 1;	
		}
	}

?>