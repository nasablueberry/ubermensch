<?php
require_once('DBconnect.php');
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id = (int)($_POST['program_id']??0);
    $name = $_POST['program_name'] ?? null;
    $provider = $_POST['provider_id'] ?: null;
    $desc = $_POST['description'] ?? null;
    $income = $_POST['eligibility_income_threshold'] ?: null;
    $cgpa = $_POST['eligibility_cgpa_threshold'] ?: null;
    $total = $_POST['total_funds'] ?: 0;
    $funds_remaining = $_POST['funds_remaining'] ?: $total;
    $start = $_POST['start_date'] ?: null;
    $end = $_POST['end_date'] ?: null;

    $stmt = $conn->prepare('UPDATE ScholarshipProgram SET program_name = ?, description = ?, eligibility_income_threshold = ?, eligibility_cgpa_threshold = ?, total_funds = ?, funds_remaining = ?, start_date = ?, end_date = ?, provider_id = ? WHERE program_id = ?');
    $stmt->bind_param('ssddddssii', $name, $desc, $income, $cgpa, $total, $funds_remaining, $start, $end, $provider, $id);
    try{ $stmt->execute(); header('Location: show_programs.php'); exit; }catch(Exception $e){ echo 'Error: '.htmlspecialchars($e->getMessage()); }
}
?>