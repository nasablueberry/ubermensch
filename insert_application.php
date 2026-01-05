<?php
require_once('DBconnect.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: show_programs.php');
    exit;
}
$student_id = isset($_POST['student_id']) ? (string) $_POST['student_id'] : '';
$program_id = (int) ($_POST['program_id'] ?? 0);
$comments = $_POST['comments'] ?? null;

// basic checks
$stmt = $conn->prepare('SELECT * FROM student WHERE student_id = ?');
$stmt->bind_param('s', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) {
    echo 'Student not found';
    exit;
}

$stmt = $conn->prepare('SELECT * FROM scholarshipprogram WHERE program_id = ?');
$stmt->bind_param('i', $program_id);
$stmt->execute();
$program = $stmt->get_result()->fetch_assoc();
if (!$program) {
    echo 'Program not found';
    exit;
}

// check program active period
$now = date('Y-m-d');
if (($program['start_date'] && $now < $program['start_date']) || ($program['end_date'] && $now > $program['end_date'])) {
    echo 'Program is not currently accepting applications.';
    exit;
}

// Duplicate aid detection: block if student has any approved or pending application to any program
// Duplicate aid detection: block if student has any approved or pending application to any program
$dupStmt = $conn->prepare("SELECT COUNT(*) AS c FROM application WHERE student_id = ? AND status IN ('pending','approved')");
$dupStmt->bind_param('s', $student_id);
$dupStmt->execute();
$dup = $dupStmt->get_result()->fetch_assoc();
if ($dup['c'] > 0) {
    // log duplicate
    $log = $conn->prepare('INSERT INTO duplicateaidlog (notes, student_id, conflicting_program_id) VALUES (?, ?, ?)');
    $note = 'Attempt to apply while having active/pending application';
    $log->bind_param('ssi', $note, $student_id, $program_id);
    $log->execute();
    echo 'You already have an active or pending application. Duplicate aid not allowed.';
    exit;
}

// eligibility check
$income_ok = 1;
$cgpa_ok = 1;
if ($program['eligibility_income_threshold'] !== null && $program['eligibility_income_threshold'] !== '') {
    $income_ok = ($student['household_income'] <= $program['eligibility_income_threshold']) ? 1 : 0;
}
if ($program['eligibility_cgpa_threshold'] !== null && $program['eligibility_cgpa_threshold'] !== '') {
    $cgpa_ok = ($student['gpa'] >= $program['eligibility_cgpa_threshold']) ? 1 : 0;
}
$overall = ($income_ok && $cgpa_ok) ? 1 : 0;

$chk = $conn->prepare('INSERT INTO eligibility_check (income_ok, cgpa_ok, overall_eligible, student_id, program_id) VALUES (?, ?, ?, ?, ?)');
$chk->bind_param('iiisi', $income_ok, $cgpa_ok, $overall, $student_id, $program_id);
$chk->execute();

if (!$overall) {
    // insert application as rejected for auditability
    $ins = $conn->prepare('INSERT INTO application (status, comments, student_id, program_id, review_date) VALUES (?, ?, ?, ?, NOW())');
    $stat = 'rejected';
    $ins->bind_param('sssi', $stat, $comments, $student_id, $program_id);
    $ins->execute();
    echo 'You are not eligible for this program based on eligibility rules.';
    exit;
}

// insert pending application
$ins = $conn->prepare('INSERT INTO application (status, comments, student_id, program_id) VALUES (?, ?, ?, ?)');
$stat = 'pending';
$ins->bind_param('sssi', $stat, $comments, $student_id, $program_id);
$ins->execute();
header('Location: student_dashboard.php');
exit;
?>