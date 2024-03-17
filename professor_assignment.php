<?php

session_start(); // Start session (or resume if already started)

$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
$professor_id = isset($_SESSION['professor_id']) ? $_SESSION['professor_id'] : '';

require_once('db_connection.php'); // Include your connection file

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
          <li class="welcomeuser"><a href="#"><?php echo $logged_in_user; ?></a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Log in</a></li>
          <li> <a href="register.php">Sign up</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <?php


  // Retrieve students using joins (assuming $conn is established in your db_connection.php)

  try {
      $stmt_students = $conn->prepare("
          SELECT DISTINCT s.studentid, s.first_name, s.last_name, s.email
          FROM students s
          INNER JOIN student_professor sp ON sp.studentid = s.studentid
          WHERE sp.professorid = ?;
      ");
      $stmt_students->execute([$professor_id]);
      $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
  } catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

  ?>
<div class = "wholepage">
  <h2 class = "students">Students</h2>
  <table class = "studenttable">
    <thead class = "studenttablehead">
      <tr class = "studentsheaders">
        <th>Student ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Assignments</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $student): ?>
        <tr class = "studentsrows">
          <td><?php echo $student['studentid']; ?></td>
          <td><?php echo $student['first_name']; ?></td>
          <td><?php echo $student['last_name']; ?></td>
          <td><?php echo $student['email']; ?></td>
          <td>
            <?php
              $student_id = $student['studentid'];

              // Retrieve assignments for this student
              $stmt_assignments = $conn->prepare("
                  SELECT name, a.class, a.due_date, a.recommended_date, a.description
                  FROM assignments a
                  INNER JOIN student_professor sp ON sp.assignmentid = a.assignmentid
                  WHERE sp.studentid = ?;
              ");
              $stmt_assignments->execute([$student_id]);
              $assignments = $stmt_assignments->fetchAll(PDO::FETCH_ASSOC);
              ?>
      
      <?php
              // Check if assignments exist and display them
              if ($assignments) {
                  echo '<table class = "assignments">'; // Start nested table for assignments
                  echo "<thead>";
                  echo "<tr>";
                  echo "<th>Assignment Name</th>";
                  echo "<th>Class</th>";
                  echo "<th>Due Date</th>";
                  echo "<th>Recommended Date</th>";
                  echo "<th>Description</th>";
                  echo "</tr>";
                  echo "</thead>";
                  echo "<tbody>";
                  // Loop through retrieved assignments
                  foreach ($assignments as $assignment) {
                      echo "<tr>";
                      echo "<td>" . $assignment['name'] . "</td>";
                      echo "<td>" . $assignment['class'] . "</td>";
                      echo "<td>" . $assignment['due_date'] . "</td>";
                      echo "<td>" . $assignment['recommended_date'] . "</td>";
                      echo "<td>" . $assignment['description'] . "</td>";
                      echo "</tr>";
                  }
                  echo "</tbody>";
                  echo "</table>"; // End nested table for assignments
              } else {
                  echo "No assignments found for this student.";
              }
            ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
<div>
  
    <div class = "bkimg"> </div>
    <div class = "footer">
        <p class = "footertext">  Tech Innovation Hub &copy; 2024 </p>
    </div>

    
</body>
</html>