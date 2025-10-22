<?php
session_start();
session_destroy();
// FUNCTIONAL FIX: Redirect directly to login.php
header("Location: login.php");
?>