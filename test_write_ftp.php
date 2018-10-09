<?php

	$file = 'somefile.txt';
	
	// create your file in the local file system here.
	
	$remote_file = 'readme.txt';
	
	// set up basic connection
	//$conn_id = ssh2_connect("203.154.94.107",22) or die("what");
	file_get_contents('ssh2.sftp://ktb_ftp:*xu2YM_N@203.154.94.107:22/ktb_import/production/test.txt') or die("what");
	
	

?>