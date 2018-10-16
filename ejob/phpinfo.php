<?php

$server_ip=$_SERVER[SERVER_ADDR];
echo $server_ip;

// Show all information, defaults to INFO_ALL
echo "current php date: ". date("Y-m-d H:i:s");
echo "<BR>current mysql date ->
'<b>SELECT NOW( ) AS date
FROM members
LIMIT 0 , 1</b>' -> ";

phpinfo();

//include('db_connect.php');
//include('./tll_functions.php');
/*
echo getFirstItem("SELECT NOW( ) AS date
FROM members
LIMIT 0 , 1");*/



?>