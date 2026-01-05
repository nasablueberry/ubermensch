<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit;
}

$student = $_SESSION['user'];
$student_id = $student['student_id'];

// Refresh student data to get latest verification status
$stmt = $conn->prepare("SELECT * FROM Student WHERE student_id=?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $student = $res->fetch_assoc();
    $_SESSION['user'] = $student;
}

// Fetch Applications
$apps_stmt = $conn->prepare("SELECT a.*, s.program_name FROM Application a JOIN ScholarshipProgram s ON a.program_id = s.program_id WHERE a.student_id=? ORDER BY application_date DESC");
$apps_stmt->bind_param("s", $student_id);
$apps_stmt->execute();
$applications = $apps_stmt->get_result();

// Fetch Available Programs (Limit 3)
$prog_query = "SELECT * FROM ScholarshipProgram WHERE funds_remaining > 0 ORDER BY start_date DESC LIMIT 3";
$programs = mysqli_query($conn, $prog_query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Übermensch – Student Dashboard</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>

    <main class="container my-5">
        <div class="row">
            <!-- Sidebar / Profile Summary -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['name']); ?>&background=0d3b66&color=fff&size=128"
                            class="rounded-circle mb-3">
                        <h4 class="card-title"><?php echo htmlspecialchars($student['name']); ?></h4>
                        <p class="text-muted mb-1">ID: <?php echo htmlspecialchars($student['student_id']); ?></p>

                        <?php if ($student['verification_status'] == 'Verified'): ?>
                            <span class="badge bg-success rounded-pill px-3 py-2">Verified Student</span>
                        <?php else: ?>
                            <span
                                class="badge bg-warning text-dark rounded-pill px-3 py-2"><?php echo htmlspecialchars($student['verification_status']); ?></span>
                        <?php endif; ?>

                        <hr>
                        <div class="text-start">
                            <p><strong>CGPA:</strong> <?php echo htmlspecialchars($student['cgpa']); ?></p>
                            <p><strong>Income:</strong>
                                $<?php echo htmlspecialchars(number_format($student['household_income'], 2)); ?></p>
                            <p><strong>District:</strong> <?php echo htmlspecialchars($student['district']); ?></p>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="student_profile.php" class="btn btn-outline-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Stats -->
                <div class="stats-container mb-4">
                    <div class="stat-card">
                        <h2><?php echo $applications->num_rows; ?></h2>
                        <p>My Applications</p>
                    </div>
                    <div class="stat-card">
                        <h2><?php echo mysqli_num_rows($programs); ?></h2>
                        <p>New Programs</p>
                    </div>
                </div>

                <!-- Applications -->
                <h3 class="section-title">My Applications</h3>
                <div class="custom-table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($applications->num_rows > 0): ?>
                                <?php while ($app = $applications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['program_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($app['application_date'])); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'bg-secondary';
                                            if ($app['status'] == 'approved')
                                                $statusClass = 'bg-success';
                                            if ($app['status'] == 'rejected')
                                                $statusClass = 'bg-danger';
                                            if ($app['status'] == 'pending')
                                                $statusClass = 'bg-warning text-dark';
                                            ?>
                                            <span
                                                class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($app['status']); ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No applications submitted yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Available Programs -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title m-0">Recommended For You</h3>
                    <a href="show_programs.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="row">
                    <?php foreach ($programs as $prog): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <?php echo htmlspecialchars($prog['program_name']); ?></h5>
                                    <p class="card-text small text-muted mb-2">
                                        Funds: $<?php echo number_format($prog['funds_remaining']); ?> left
                                    </p>
                                    <a href="apply_program.php?id=<?php echo $prog['program_id']; ?>"
                                        class="btn btn-sm btn-primary-custom">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </main>

    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>