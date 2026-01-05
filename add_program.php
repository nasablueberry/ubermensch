<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'provider') {
    header("Location: login.php");
    exit;
}
$provider_id = $_SESSION['user']['provider_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Program - Übermensch</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Post New Scholarship Program</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="insert_program.php">
                            <input type="hidden" name="provider_id"
                                value="<?php echo htmlspecialchars($provider_id); ?>">

                            <div class="mb-3">
                                <label class="form-label">Program Name</label>
                                <input type="text" name="program_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Eligibility Income Threshold (≤)</label>
                                    <input type="number" step="0.01" name="eligibility_income_threshold"
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Eligibility CGPA Threshold (≥)</label>
                                    <input type="number" step="0.01" name="eligibility_cgpa_threshold"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Funds</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="total_funds" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Create Program</button>
                                <a href="provider_dashboard.php" class="btn btn-outline-secondary">Cancel</a>
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