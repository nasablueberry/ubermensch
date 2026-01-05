<?php
require_once('DBconnect.php');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['provider_id'];
    $name = $_POST['name'] ?? null;
    $type = $_POST['provider_type'] ?? null;
    $email = $_POST['contact_email'] ?? null;
    $phone = $_POST['contact_phone'] ?? null;
    $address = $_POST['address'] ?? null;

    $stmt = $conn->prepare('UPDATE AidProvider SET name = ?, provider_type = ?, contact_email = ?, contact_phone = ?, address = ? WHERE provider_id = ?');
    $stmt->bind_param($id, $name, $type, $email, $phone, $address);
    try {
        $stmt->execute();
        header('Location: show_providers.php');
        exit;
    } catch (Exception $e) {
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>