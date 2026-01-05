<?php
require_once('DBconnect.php');
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user_type'] != 'provider' && $_SESSION['user_type'] != 'admin')) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: show_programs.php');
    exit;
}
$id = (int) $_GET['id'];
$stmt = $conn->prepare('SELECT * FROM ScholarshipProgram WHERE program_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

// Security: Provider can only edit own program
if ($_SESSION['user_type'] == 'provider' && $row['provider_id'] != $_SESSION['user']['provider_id']) {
    echo "Access Denied";
    exit;
}

$providers = $conn->query('SELECT provider_id, name FROM AidProvider');
if (!$row) {
    echo 'Program not found';
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Program</title>
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main>
        <h1>Edit Program</h1>
        <form method="post" action="update_program.php">
            <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($row['program_id']); ?>">
            Name: <input name="program_name" value="<?php echo htmlspecialchars($row['program_name']); ?>"><br>
            Provider: <select
                name="provider_id"><?php while ($p = $providers->fetch_assoc()) {
                    $sel = $p['provider_id'] == $row['provider_id'] ? ' selected' : '';
                    echo '<option value="' . htmlspecialchars($p['provider_id']) . '"' . $sel . '>' . htmlspecialchars($p['name']) . '</option>';
                } ?></select><br>
            Description: <textarea
                name="description"><?php echo htmlspecialchars($row['description']); ?></textarea><br>
            Income threshold: <input name="eligibility_income_threshold" step="0.01"
                value="<?php echo htmlspecialchars($row['eligibility_income_threshold']); ?>"><br>
            CGPA threshold: <input name="eligibility_cgpa_threshold" step="0.01"
                value="<?php echo htmlspecialchars($row['eligibility_cgpa_threshold']); ?>"><br>
            Total funds: <input name="total_funds" step="0.01"
                value="<?php echo htmlspecialchars($row['total_funds']); ?>"><br>
            Funds remaining: <input name="funds_remaining" step="0.01"
                value="<?php echo htmlspecialchars($row['funds_remaining']); ?>"><br>
            Start: <input type="date" name="start_date" value="<?php echo htmlspecialchars($row['start_date']); ?>"><br>
            End: <input type="date" name="end_date" value="<?php echo htmlspecialchars($row['end_date']); ?>"><br>
            <input type="submit" value="Save">
        </form>
        <p><a href="show_programs.php">Back</a></p>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>