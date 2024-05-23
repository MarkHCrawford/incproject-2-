<?php
session_start(); // Start session (or resume if already started)
$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
$professor_id = isset($_SESSION['professor_id']) ? $_SESSION['professor_id'] : '';

require_once('db_connection.php'); // Include your connection file




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentid = $_POST['studentid'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $professor_id = $_SESSION['professor_id'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO students (studentid, username, password, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentid, $username, $hashed_password, $first_name, $last_name, $email]);

        $stmt = $conn->prepare("INSERT INTO student_professor (studentid, professorid) VALUES (?, ?)");
        $stmt->execute([$studentid, $professor_id]);
        

        $success_message = "Student added successfully!";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/professor_assignment.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <title>Add Student</title>
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="info.php">Info</a></li>
        <li><a href="professor_assignment.php">Assignments</a></li>
        <?php if ($logged_in_user): ?>
          <li class="welcomeuser"><a href="#"><?php echo $logged_in_user; ?></a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Log in</a></li>
          <li><a href="register.php">Sign up</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <div class = addstudentsep>
  <?php if (!empty($success_message)): ?>
    <div><?php echo $success_message; ?></div>
  <?php endif; ?>
  <form method="POST" action="add_student.php">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="text" name="studentid" placeholder="Student ID" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input class="copybutton" type="submit" value="Add Student">
  </form>
  <a class="lower" href = "professor_assignment.php">Return to Students Page</a>
  </div>
  <div class="bkimg"></div>
  <div class="footer">
    <p class="footertext">Tech Innovation Hub &copy; 2024</p>
  </div>
</body>
</html>
