<?php
session_start();
$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
$professor_id = isset($_SESSION['professor_id']) ? $_SESSION['professor_id'] : '';
require_once('db_connection.php');


$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Retrieve form data
    $name = $_POST['name'];
    $class = $_POST['class'];
    $due_date = $_POST['due_date'];
    $recommended_date = $_POST['recommended_date'];
    $description = $_POST['description'];
    $completed = 0;


    // Be sure to fix this and auto increment assignment id -- Will be problem with multiple users
    try {
        // Insert assignment into the database
       
        // Query to find the maximum assignment ID
        $stmt_max_id = $conn->query("SELECT MAX(assignmentid) AS max_id FROM assignments");
        $max_id_row = $stmt_max_id->fetch(PDO::FETCH_ASSOC);
        
        // Assign a new assignment ID
        $assignment_id = $max_id_row['max_id'] + 1;
        
        $stmt = $conn->prepare("INSERT INTO assignments (assignmentid, name, class, due_date, recommended_date, description, completed) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$assignment_id, $name, $class, $due_date, $recommended_date, $description, $completed]);

        // Retrieve the assignment ID
     //  $assignment_id = $conn->lastInsertId();
        
        // Link the assignment to the student
        $stmt_link = $conn->prepare("INSERT INTO student_professor (studentid, professorid, assignmentid) VALUES (?, ?, ?)");
        $stmt_link->execute([$student_id, $professor_id, $assignment_id]);

        
        $success_message = "Assignment added successfully!";
        
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} elseif (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
} else {
    // If student ID is not provided, redirect to professor_assignment.php
    header("Location: professor_assignment.php");
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
  <title>Add Assignments</title>
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
    <h2 class = "assignmenthead">Add Assignment</h2>
    <div class = "addstudentsep">
    <form method="POST" action="add_assignment.php?student_id=<?php echo $student_id; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="class">Class:</label>
        <input type="text" id="class" name="class" required><br>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" required><br>

        <label for="recommended_date">Recommended Date:</label>
        <input type="date" id="recommended_date" name="recommended_date" required><br>
        <label for = "description">Description:</label>
        <textarea cols=50 rows=5 id="description" name="description" required></textarea><br>

        <input class = "copybutton" type="submit" value="Add Assignment">
    </form>
    </div>
    <a class="backtostudent" href="view_student.php?id=<?php echo $student_id; ?>">Back to View Student</a>
  <div class="bkimg"></div>
  <div class="footer">
    <p class="footertext">Tech Innovation Hub &copy; 2024</p>
  </div>
</body>
</html>
