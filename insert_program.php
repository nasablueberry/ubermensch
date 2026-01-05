<?php
require_once('DBconnect.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['program_name'] ?? null;
    $provider = $_POST['provider_id'] ?: null;
    $description = $_POST['description'] ?? null;
    $income_th = $_POST['eligibility_income_threshold'] ?: null;
    $cgpa_th = $_POST['eligibility_cgpa_threshold'] ?: null;
    $total = $_POST['total_funds'] ?: 0;
    $start = $_POST['start_date'] ?: null;
    $end = $_POST['end_date'] ?: null;

    $stmt = $conn->prepare('INSERT INTO ScholarshipProgram (program_name, description, eligibility_income_threshold, eligibility_cgpa_threshold, total_funds, funds_remaining, start_date, end_date, provider_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $funds_remaining = $total;
    $stmt->bind_param('ssddddssi', $name, $description, $income_th, $cgpa_th, $total, $funds_remaining, $start, $end, $provider);
    try {
        $stmt->execute();
        header('Location: provider_dashboard.php');
        exit;
    } catch (Exception $e) {
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>