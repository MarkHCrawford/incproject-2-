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
    <link rel="stylesheet" href="css/defaultassignment.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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

    <div class = "notify"> 
        <h1> BMCC Complete </h1>
        <p> Please log in to view assignments. </p>
        <input id = "button" type = "button" value = "Log in" onclick = "window.location.href = 'login.php'">
</div>

    <div class = "bkimg"> </div>
    <div class = "footer">
        <p class = "footertext">  Tech Innovation Hub &copy; 2024 </p>
    </div>

    
</body>
</html>