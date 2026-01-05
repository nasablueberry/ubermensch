<?php
require_once('DBconnect.php');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = (string) $_POST['student_id'];
    $name = $_POST['name'] ?? '';
    $present_address = $_POST['present_address'] ?? '';
    $permanent_address = $_POST['permanent_address'] ?? '';
    $gpa = $_POST['gpa'] !== '' ? (float) $_POST['gpa'] : null;
    $attendance = $_POST['attendance'] !== '' ? (float) $_POST['attendance'] : null;

    $stmt = $conn->prepare("UPDATE student SET name = ?, present_address = ?, permanent_address = ?, gpa = ?, attendance = ? WHERE student_id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('sssdds', $name, $present_address, $permanent_address, $gpa, $attendance, $student_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo 'Error updating student: ' . htmlspecialchars($stmt->error);
    }
}
?>