<?php
require_once('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// student_id is a VARCHAR in the schema; do not cast to int.
	$student_id = trim((string) ($_POST['student_id'] ?? ''));
	if ($student_id === '') {
		// generate a short unique id if the form did not provide one
		try {
			$student_id = bin2hex(random_bytes(8));
		} catch (Exception $e) {
			$student_id = uniqid('s', true);
		}
	}

	$name = $_POST['name'] ?? null;
	$dob = $_POST['dob'] ?? null;
	$gender = $_POST['gender'] ?? null;
	$gpa = $_POST['gpa'] !== '' ? (float) $_POST['gpa'] : null;
	$attendance = $_POST['attendance'] !== '' ? (float) $_POST['attendance'] : null;
	$household_income = $_POST['household_income'] !== '' ? (float) $_POST['household_income'] : null;
	$birth_certificate_id = $_POST['birth_certificate_id'] ?? null;
	$present_address = $_POST['present_address'] ?? null;
	$permanent_address = $_POST['permanent_address'] ?? null;
	$district = $_POST['district'] ?? null;
	$verification_status = $_POST['verification_status'] ?? 'unverified';
	$institution_id = isset($_POST['institution_id']) && $_POST['institution_id'] !== '' ? (int) $_POST['institution_id'] : null;
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

	$stmt = $conn->prepare("INSERT INTO student (student_id, name, DOB, gender, gpa, attendance_percentage, household_income, birth_certificate_id, address, district, verification_status, institution_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	if ($stmt === false) {
		die('Prepare failed: ' . htmlspecialchars($conn->error));
	}

	// types: s student_id, s name, s DOB, s gender, d gpa, d attendance, d household_income,
	// s birth_certificate_id, s address, s district, s verification_status, i institution_id, s password
	$types = 'ssssdddssssis';
	$bindVars = [$student_id, $name, $dob, $gender, $gpa, $attendance, $household_income, $birth_certificate_id, $present_address, $district, $verification_status, $institution_id, $password];

	// mysqli requires references for call_user_func_array
	$tmp = [];
	$tmp[] = $types;
	foreach ($bindVars as $k => $v) {
		$tmp[] = &$bindVars[$k];
	}
	call_user_func_array([$stmt, 'bind_param'], $tmp);

	try {
		$stmt->execute();
		header('Location: admin_dashboard.php');
		exit;
	} catch (Exception $e) {
		// on error go back to add form with a message (preserve project behavior)
		header('Location: add_student.php');
		exit;
	}
}
?>