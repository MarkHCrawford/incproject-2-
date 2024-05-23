<?php
session_start();
require_once('db_connection.php');
$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
$professor_id = isset($_SESSION['professor_id']) ? $_SESSION['professor_id'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id'])) {
    $student_id = $_GET['id'];

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, username = ?, password = ? WHERE studentid = ?");
        $stmt->execute([$first_name, $last_name, $email, $username, $password, $student_id]);
        header("Location: professor_assignment.php");
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM students WHERE studentid = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    try {
        $stmt_assignments = $conn->prepare("SELECT * FROM assignments WHERE assignmentid IN (SELECT assignmentid FROM student_professor WHERE studentid = ?)");
        $stmt_assignments->execute([$student_id]);
        $assignments = $stmt_assignments->fetchAll(PDO::FETCH_ASSOC);
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
  <title>Professor Dashboard</title>
</head>
<div>
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
  <form method="POST" action="edit_student.php?id=<?php echo $student_id; ?>">
    <input type="text" name="first_name" value="<?php echo $student['first_name']; ?>" required>
    <input type="text" name="last_name" value="<?php echo $student['last_name']; ?>" required>
    <input type="email" name="email" value="<?php echo $student['email']; ?>" required>
    <input type="text" name="username" value="<?php echo $student['username']; ?>" required>
    <input type="password" name="password" value="<?php echo $student['password']; ?>" required>
    <input type="submit" value="Update Student">
  </form>
  <h2 class = "students">Assignments for <?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h2>
  <div class = "addassignmentbutton">
  <a class="moveright" href="add_assignment.php?student_id=<?php echo $student_id; ?>">Add Assignment</a>
        </div>
  <table class = "studenttable">
    <thead class = "studenttablehead">
      <tr>
        <th>Assignment ID</th>
        <th>Name</th>
        <th>Class</th>
        <th>Due Date</th>
        <th>Recommended Date</th>
        <th>Is Complete?</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($assignments as $assignment): ?>
        <tr>
          <td><?php echo $assignment['assignmentid']; ?></td>
          <td><?php echo $assignment['name']; ?></td>
          <td><?php echo $assignment['class']; ?></td>
          <td><?php echo $assignment['due_date']; ?></td>
          <td><?php echo $assignment['recommended_date']; ?></td>
          <?php if ($assignment['completed']): ?>
            <td>Yes</td>
          <?php else: ?>
            <td>No</td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class = "returntomainpagebutton">
  <a class="changecolor" href="professor_assignment.php">Return to Main Student Page</a>
    </div>
<div class="bkimg"></div>
<div class="footer">
    <p class="footertext">Tech Innovation Hub &copy; 2024</p>
  </div>
  </body>
</html>
