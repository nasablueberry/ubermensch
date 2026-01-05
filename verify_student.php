<?php
session_start();
require_once('DBconnect.php');

// Security check: Ensure admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $student_id = $_GET['id'];
    $action = $_GET['action'];
    $status = 'pending';

    if ($action == 'verify')
        $status = 'Verified';
    if ($action == 'reject')
        $status = 'Rejected';

    $stmt = $conn->prepare("UPDATE Student SET verification_status=? WHERE student_id=?");
    $stmt->bind_param("ss", $status, $student_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Verification updated");
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    header("Location: admin_dashboard.php");
}
?>