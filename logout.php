<?php
session_start();
session_destroy();
// Redirect directly to login.php
header("Location: login.php");
?>