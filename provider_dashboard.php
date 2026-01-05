<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'provider') {
    // Fallback: if provider_dashboard accessed without login, try redirect
    header("Location: login.php");
    exit;
}

$provider = $_SESSION['user'];
$provider_id = $provider['provider_id'];

// Stats
$programs_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM ScholarshipProgram WHERE provider_id=$provider_id"))['c'];
$apps_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM Application a JOIN ScholarshipProgram p ON a.program_id=p.program_id WHERE p.provider_id=$provider_id"))['c'];
$funds_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(funds_remaining) as f FROM ScholarshipProgram WHERE provider_id=$provider_id"));
$total_funds = $funds_res['f'] ?? 0;

// Recent Applications
$recent_apps_sql = "SELECT a.*, s.name as student_name, p.program_name 
                    FROM Application a 
                    JOIN ScholarshipProgram p ON a.program_id = p.program_id 
                    JOIN Student s ON a.student_id = s.student_id
                    WHERE p.provider_id = $provider_id 
                    ORDER BY a.application_date DESC LIMIT 5";
$recent_apps = mysqli_query($conn, $recent_apps_sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Übermensch – Provider Dashboard</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>

    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Hello,
                    <?php echo htmlspecialchars($provider['name']); ?>
                </h1>
                <p class="text-muted">Manage your scholarship programs and applications</p>
            </div>
            <a href="add_program.php" class="btn btn-success">+ Post New Program</a>
        </div>

        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <h2>
                    <?php echo $programs_count; ?>
                </h2>
                <p>Active Programs</p>
            </div>
            <div class="stat-card">
                <h2>
                    <?php echo $apps_count; ?>
                </h2>
                <p>Total Applicants</p>
            </div>
            <div class="stat-card">
                <h2>$
                    <?php echo number_format($total_funds); ?>
                </h2>
                <p>Funds Available</p>
            </div>
        </div>

        <div class="row">
            <!-- Managament -->
            <div class="col-md-12">
                <h3 class="section-title">Recent Applications</h3>
                <div class="custom-table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Program</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_apps && mysqli_num_rows($recent_apps) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($recent_apps)): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($row['student_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($row['program_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($row['application_date'])); ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?php echo ($row['status'] == 'approved' ? 'bg-success' : ($row['status'] == 'rejected' ? 'bg-danger' : 'bg-warning text-dark')); ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Review Link (assuming created later or using basic update) -->
                                            <a href="review_application.php?id=<?php echo $row['application_id']; ?>"
                                                class="btn btn-sm btn-outline-primary">Review</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No applications received yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end">
                    <a href="show_programs.php?my_programs=1" class="btn btn-outline-secondary">View All My Programs</a>
                </div>
            </div>
        </div>
    </main>

    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>