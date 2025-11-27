<?php
session_start();
session_unset();// Elimimar las variables
session_destroy(); 
header("Location: login.php");
exit();
?>