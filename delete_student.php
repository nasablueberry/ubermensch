<?php
require_once('DBconnect.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $student_id = (string) $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
    $stmt->bind_param('s', $student_id);
    try {
        $stmt->execute();
        header("Location: admin_dashboard.php");
        exit;
    } catch (Exception $e) {
        echo "Error deleting student: " . $e->getMessage();
    }
}
?>