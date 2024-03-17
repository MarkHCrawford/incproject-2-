<?php

session_start(); // Start session (or resume if already started)

$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
    <title>Signup</title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="info.php">Info</a></li>
                <li><a href="defaultassignment.php">Assignments</a></li>
        <li><a href="login.php">Login</a></li>
        <li> <a href="register.php">Sign up</a></li>
            </ul>
        </nav>
    </header>

    <div class="page">
  <div class="container">
    <div class="left">
      <div class="signup">Register</div>
      <div class="eula">Enter your information to create an account.</div>
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
        <form action = "register.php" method = "post">





      <?php

// Include connection file
require_once 'db_connection.php';

// Error message (initialize as empty)
$error_message = "";

// Process form submission (if submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $id = trim($_POST['id']);
  $first_name = trim($_POST['first_name']);
  $last_name = trim($_POST['last_name']);
  $email = trim($_POST['email']);
  $account_type = trim($_POST['accttype']);

  // Basic validation (replace with more comprehensive checks)
  if (empty($username) || empty($password) || empty($account_type) || empty($first_name) || empty($last_name) || empty($email)) {
    $error_message = "Please fill in all required fields.";
  } else {
    // Check for existing username (based on chosen account type)
    $sql_check_username = "";
    if ($account_type === "student") {
      $sql_check_username = "SELECT COUNT(*) AS username_count FROM Students WHERE username = :username";
    } else {
      $sql_check_username = "SELECT COUNT(*) AS username_count FROM Professors WHERE username = :username";
    }
    $stmt_check_username = getConnection()->prepare($sql_check_username);
    $stmt_check_username->bindValue(":username", $username);
    $stmt_check_username->execute();
    $username_count_result = $stmt_check_username->fetch(PDO::FETCH_ASSOC);
    $username_count = (int) $username_count_result['username_count'];

    if ($username_count > 0) {
      $error_message = "Username already exists.";
    } else {
      // Insert data based on account type
      $sql_insert = "";
      if ($account_type === "student") {
        $sql_insert = "INSERT INTO Students (studentid, username, password, first_name, last_name, email) 
                       VALUES (:id, :username, :password, :first_name, :last_name, :email)";
      } else {
        $sql_insert = "INSERT INTO Professors (professorid, username, password, first_name, last_name, email) 
                       VALUES (:id, :username, :password, :first_name, :last_name, :email)";
      }
      
      $stmt_insert = getConnection()->prepare($sql_insert);
      $stmt_insert->bindvalue(":id", $id);
      $stmt_insert->bindValue(":username", $username);
      $stmt_insert->bindValue(":password", password_hash($password, PASSWORD_BCRYPT)); // Hash password securely
      $stmt_insert->bindValue(":first_name", $first_name);
      $stmt_insert->bindValue(":last_name", $last_name);
      $stmt_insert->bindValue(":email", $email);
      $stmt_insert->execute();

      if ($stmt_insert->rowCount() > 0) { // Check if insertion was successful
        header("Location: login.php?success=registration");
        exit;
      } else {
        $error_message = "Registration failed. Please try again.";
      }
    }
  }
}

?>

<?php if (!empty($error_message)): ?>
  <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>









        <table>
          <tr>
            <td>
        <label for="username">Username</label>
        <input type="username" id="username" name="username">
        <hr>
</td><td>
        <label for="password">Password</label>
        <input type="password" id="password" name="password">
        <hr>
</td></tr>
        <label for="id"> ID </label>
        <input type="id" id="id" name="id">
        <hr>
        <tr>
          <td>
        <label for="first name">First Name </label>
        <input type="first name" id="first name" name="first_name">
        <hr>
</td><td>
        <label for="last name">Last Name </label>
        <input type="last name" id="last name" name="last_name">
        <hr>
</td></tr>
<tr>
<td>
        <label for="email">Email</label>
        <input type="email" id="email" name="email">
        <hr>
</td><td>
        <label for = "accttype"> Account Type </label>
        <select id="accttype" name="accttype">
          <option value="student" name="student">Student</option>
          <option value="faculty" name ="professor">Faculty</option>
</select>
</td></tr>
        <table>
        <input type="submit" id="submit" value="Submit">
        
      </div>
      
    </div>
    
  </div>
  
</div>
</form>




    <div class = "bkimg"> </div>
    <div class = "footer">
          <p class = "footertext">  Tech Innovation Hub &copy; 2024 </p>
    </div>

    <script scr="js/login.js"></script>
</body>
</html>