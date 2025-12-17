<?php
session_start();
session_destroy();
// Redirect ke login.php, bukan index.php
header("location:login.php"); 
exit;
?>