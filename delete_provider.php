<?php
require_once('DBconnect.php');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare('DELETE FROM AidProvider WHERE provider_id = ?');
    $stmt->bind_param('i', $id);
    try {
        $stmt->execute();
        header('Location: show_providers.php');
        exit;
    } catch (Exception $e) {
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>