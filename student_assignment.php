<?php
session_start(); // Start session (or resume if already started)

$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';

require_once('db_connection.php');

// Redirect to login page if the student is not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$success_message = '';

// Handle marking an assignment as complete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assignment_id'])) {
    $assignment_id = $_POST['assignment_id'];

    try {
        $stmt = $conn->prepare("UPDATE assignments SET completed = 1 WHERE assignmentid = ?");
        $stmt->execute([$assignment_id]);
        $success_message = "";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch classes and assignments
try {
    // Get assignments for the student
    $stmt = $conn->prepare("
        SELECT 
            a.assignmentid, a.name, a.class, a.recommended_date, a.completed, 
            p.last_name AS professor_last_name
        FROM assignments a
        JOIN student_professor sp ON a.assignmentid = sp.assignmentid
        JOIN professors p ON sp.professorid = p.professorid
        WHERE sp.studentid = ?
        ORDER BY a.class, a.due_date ASC
    ");
    $stmt->execute([$student_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function days_until_due($recommended_date) {
    $current_date = new DateTime();
    $recommended_date = new DateTime($recommended_date);
    return $current_date->diff($recommended_date)->days;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/student_assignment.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Student Assignments</title>
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
                    <li><a href="register.php">Sign up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <h2 class="yourassignments">Your Assignments</h2>
<table class="assignmenttable">
    <?php if ($success_message): ?>
        <tr>
            <td colspan="5"><p class="success"><?php echo $success_message; ?></p></td>
        </tr>
    <?php endif; ?>
    <?php
    $current_class = '';
    foreach ($assignments as $assignment):
        if ($assignment['class'] !== $current_class):
            $current_class = $assignment['class'];
    ?>
        <tr>
            <td colspan="5"><h3 class="classname"><?php echo htmlspecialchars($current_class); ?></h3></td>
        </tr>
    <?php endif; ?>
        <tr class="assignmentitem <?php echo (days_until_due($assignment['recommended_date']) < 7 && !$assignment['completed']) ? 'urgent' : ''; ?>">
            <td><strong>Name:</strong> <?php echo htmlspecialchars($assignment['name']); ?></td>
            <td><strong>Due Date:</strong> <?php echo htmlspecialchars($assignment['recommended_date']); ?></td>
            <td><strong>Days Until Due:</strong> <?php echo days_until_due($assignment['recommended_date']); ?></td>
            <td><strong>Professor:</strong> Professor <?php echo htmlspecialchars($assignment['professor_last_name']); ?></td>
            <td>
                <?php if (!$assignment['completed']): ?>
                    <form method="POST" action="student_assignment.php">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignmentid']; ?>">
                        <input class="markcompletebutton" type="submit" value="Mark as Complete">
                    </form>
                <?php else: ?>
                    Completed
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

    <div class="bkimg"></div>
    <div class="footer">
        <p class="footertext">Tech Innovation Hub &copy; 2024</p>
    </div>
</body>
</html>
