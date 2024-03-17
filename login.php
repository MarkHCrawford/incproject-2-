<?php

session_start(); // Start session (or resume if already started)

$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
$professor_id = isset($_SESSION['professor_id']) ? $_SESSION['professor_id'] : '';
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';

?>


<?php
ob_start();

// Include connection file
require_once 'db_connection.php';

$error_message = ""; // Initialize error message

// Process form submission (if submitted for login)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $account_type = isset($_POST['accttype']) ? $_POST['accttype'] : ''; // Check if account type is selected

  // Validate input (replace with more comprehensive checks)
  if (empty($username) || empty($password) || empty($account_type)) {
    $error_message = "Please fill in all required fields.";
  } else {
    // Verify username and password based on account type
    if ($account_type === "student") {
      $sql_login = "SELECT * FROM Students WHERE username = :username";
    } else if ($account_type === "professor") {
      $sql_login = "SELECT * FROM Professors WHERE username = :username";
    } else {
      $error_message = "Invalid account type.";
    }

    $stmt_login = getConnection()->prepare($sql_login);
    $stmt_login->bindValue(":username", $username);
    $stmt_login->execute();

    if ($stmt_login->rowCount() === 1) {
      $user_data = $stmt_login->fetch(PDO::FETCH_ASSOC); // Get user data

      // Check password (using password_verify with PASSWORD_BCRYPT)
      if (password_verify($password, $user_data['password'])) {
        // Login successful! (redirect or display success message)
        session_start(); // Start session for storing user information
        $_SESSION['username'] = $username;
        $_SESSION['account_type'] = $account_type;
        if ($account_type === "student") {
          $_SESSION['student_id'] = $user_data['studentid']; // Store student ID if account type is student
        }
        else if ($account_type === "professor") {
        $_SESSION['professor_id'] = $user_data['professorid'];
        } // Store professor ID if account type is professor
        header("Location: index.php"); // Replace with appropriate redirection
        exit;
      } else {
        $error_message = "Invalid username or password.";
      }
    } else {
      $error_message = "Invalid username or password.";
    }
  }
}

?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>

<body>
    <header>
    <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="info.php">Info</a></li>
                <?php if ($account_type == 'professor'): ?>
        <li><a href="professor_assignment.php">Assignments</a></li>
        <?php elseif ($account_type == 'student'): ?>
        <li><a href="student_assignment.php">Assignments</a></li>
        <?php else: ?>
                <li><a href="defaultassignment.php">Assignments</a></li>
        <?php endif; ?>
                <?php if ($logged_in_user): ?>
      <li class = "welcomeuser"><a href="#"><?php echo $logged_in_user; ?></a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="login.php">Log in</a></li>
      <li> <a href="register.php">Sign up</a></li>
    <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="page">
  <div class="container">
    <div class="left">
      <div class="login">Login</div>
      <div class="eula">Enter your username and password to sign in.</div>
    </div>
    <div class="right">
      <svg viewBox="0 0 320 300">
        <defs>
          <linearGradient
                          inkscape:collect="always"
                          id="linearGradient"
                          x1="13"
                          y1="193.49992"
                          x2="307"
                          y2="193.49992"
                          gradientUnits="userSpaceOnUse">
            <stop
                  style="stop-color: #802BB1;"
                  offset="0"
                  id="stop876" />
            <stop
                  style="stop-color:#cb6ce6;"
                  offset="1"
                  id="stop878" />
          </linearGradient>
        </defs>
        <path d="m 40,120.00016 239.99984,-3.2e-4 c 0,0 24.99263,0.79932 25.00016,35.00016 0.008,34.20084 -25.00016,35 -25.00016,35 h -239.99984 c 0,-0.0205 -25,4.01348 -25,38.5 0,34.48652 25,38.5 25,38.5 h 215 c 0,0 20,-0.99604 20,-25 0,-24.00396 -20,-25 -20,-25 h -190 c 0,0 -20,1.71033 -20,25 0,24.00396 20,25 20,25 h 168.57143" />
      </svg>
      <div class="form">
        <form action = "login.php" method = "post">
        <label for="username">Username</label>
        <input type="username" id="username" name="username">
        <hr>
        <label for="password">Password</label>
        <input type="password" id="password" name="password">
        <hr>
        <table><tr><td>
        <label for = "accttype">Account Type </label>
        </td></tr><tr><td>
        <input type="radio" id = "student" name = "accttype" value="student">
        <label for="student" name="student">Student</label>
</td><td>
        <input type="radio" id = "professor" name = "accttype" value="professor">
        <label for="professor" name="professor">Faculty</label>
</td></tr></table>
        <input type="submit" id="submit" value="Submit">
      </div>
      <form>
    </div>
  </div>
</div>




    <div class = "bkimg"> </div>
    <div class = "footer">
          <p class = "footertext">  Tech Innovation Hub &copy; 2024 </p>
    </div>

    <script scr="js/login.js"></script>
</body>
</html>