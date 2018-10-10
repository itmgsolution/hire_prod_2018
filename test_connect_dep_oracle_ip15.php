<?php
	/* comments below are for off-line testing
	$the_output = "someVar = {
					'FIRST_NAME_THAI' : 'Prachaya'
					,'LAST_NAME_THAI' : 'Daruthep'
					,'SEX_CODE' : 'M'
					,'BIRTH_DATE' : '28'
					,'DEFORM_ID' : '16'
					,'PREFIX_NAME_ABBR' : 'Mr.'
					}";


	//echo $the_output; exit();
	*/

	//echo "try connecting oracle server for HIRE PROJECT...";

	$user = 'opp$_dba'; //nep_card
	$password = "password";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST =
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.15)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = ORCL)
				)
			)";

	//oppddb or ORCL? 192.168.3.13 or 192.168.3.15
	$tt = time();
	logTime("Open Connection");
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");

	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";

	}else{
		echo "<br><font color='red'>connection failed!</font>";
		exit;
	}
	logTime("<br>Connected");
	logTime("Send Query");

	$sql = 'SELECT count(*) the_count FROM mn_des_person';
	$stid = oci_parse($connect, $sql);
	oci_execute($stid);

	while (oci_fetch($stid)) {
		echo oci_result($stid, 'THE_COUNT') . " is ";
		echo oci_result($stid, 'THE_COUNT') . "<br>\n";
	}

	logTime("End");

	function logTime($msg){
		global $tt;
		echo "$msg (".(time() - $tt)." sec.) <br>";
		$tt = time();

	}


?>
