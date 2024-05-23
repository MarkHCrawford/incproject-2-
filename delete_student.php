<?php
session_start();
require_once('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $student_id = $_GET['id'];

    try {

        $stmt_delete_links = $conn->prepare("DELETE FROM student_professor WHERE studentid = ?");
        $stmt_delete_links->execute([$student_id]);

        // Delete the student from the students table
        $stmt_delete_student = $conn->prepare("DELETE FROM students WHERE studentid = ?");
        $stmt_delete_student->execute([$student_id]);


        // Redirect back to the professor_assignment.php page
        header("Location: professor_assignment.php");
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no student id is provided in the GET request, redirect to the professor_assignment.php page
    header("Location: professor_assignment.php");
}
?>
