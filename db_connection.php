<?php

// Database connection details (replace with your actual details)
$host = "localhost";
$dbname = "bmcccomplete";
$username = "root";
$password = "";

try {
  // Connect to the database
  $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
  exit; // Exit if connection fails
}

// Make the connection object available to other files
function getConnection() {
  global $conn;
  return $conn;
}

?>
