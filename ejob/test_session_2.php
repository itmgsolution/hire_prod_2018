<?php
// Start Session
session_start();
// Show banner
if(isset($_SESSION['MOOMIN'])) {
      echo "session message: ".$_SESSION['MESSAGE']. " ". $_SESSION['MOOMIN'];
   } else {
      echo 'Sorry, it appears session support is not enabled, or you PHP version is to old. <a href="?reload=false">Click HERE</a> to go back.<br />';
   }
   
   echo "<br>".$_SESSION['sess_userid'];
			echo "<br>".$_SESSION['sess_accesslevel'];
			echo "<br>".$_SESSION['sess_meta'];
			echo "<br>".$_SESSION['sess_user_enabled'];
?>