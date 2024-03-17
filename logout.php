<?php

session_start(); // Start session (or resume if already started)

// Destroy session data
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;

?>