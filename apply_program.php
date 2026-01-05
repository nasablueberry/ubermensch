<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: student_dashboard.php');
    exit;
}
$program_id = (int) $_GET['id'];
$student_id = $_SESSION['user']['student_id'];

$stmt = $conn->prepare('SELECT * FROM ScholarshipProgram WHERE program_id = ?');
$stmt->bind_param('i', $program_id);
$stmt->execute();
$program = $stmt->get_result()->fetch_assoc();

if (!$program) {
    echo 'Program not found';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply - Übermensch</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Apply for Scholarship</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($program['program_name']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($program['description']); ?></p>
                        <hr>

                        <form method="post" action="insert_application.php">
                            <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($program_id); ?>">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">

                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control"
                                    value="<?php echo htmlspecialchars($student_id); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Comments / Statement of Purpose</label>
                                <textarea name="comments" class="form-control" rows="4"
                                    placeholder="Why do you need this aid?"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary-custom">Submit Application</button>
                                <a href="student_dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>