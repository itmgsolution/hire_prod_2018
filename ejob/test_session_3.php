<?php
// Start Session
session_start();
// Show banner
$_SESSION['MOOMIN']  = "MOOMIN";

echo "moomin set to: ". $_SESSION['MOOMIN'];

?>