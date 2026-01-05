<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['app_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$app_id = (int) $_GET['app_id'];

// Fetch App Info
$stmt = $conn->prepare("SELECT a.*, s.name as student_name, p.program_name, p.funds_remaining 
                        FROM Application a 
                        JOIN Student s ON a.student_id=s.student_id 
                        JOIN ScholarshipProgram p ON a.program_id=p.program_id 
                        WHERE a.application_id=?");
$stmt->bind_param("i", $app_id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app || $app['status'] != 'approved') {
    echo "This application is not approved or does not exist.";
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float) $_POST['amount'];
    $method = $_POST['payment_method'];
    $ref = $_POST['transaction_reference'];

    // Call the logic from the original create_disbursement.php which we can include or replicate.
    // Replicating logic for cleaner integration
    if ($amount > $app['funds_remaining']) {
        $msg = "<div class='alert alert-danger'>Insufficient funds in program! Only \${$app['funds_remaining']} left.</div>";
    } else {
        $conn->begin_transaction();
        try {
            // Deduct Funds
            $upd = $conn->prepare('UPDATE ScholarshipProgram SET funds_remaining = funds_remaining - ? WHERE program_id = ?');
            $upd->bind_param('di', $amount, $app['program_id']);
            $upd->execute();

            // Record Transaction
            $ins = $conn->prepare('INSERT INTO Disbursement (amount_released, payment_method, transaction_reference, application_id) VALUES (?, ?, ?, ?)');
            $ins->bind_param('dssi', $amount, $method, $ref, $app_id);
            $ins->execute();

            $conn->commit();
            header("Location: admin_dashboard.php?msg=Disbursement Successful");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $msg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Disburse Funds - Admin</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Disburse Funds</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $msg; ?>
                        <p><strong>Student:</strong>
                            <?php echo htmlspecialchars($app['student_name']); ?>
                        </p>
                        <p><strong>Program:</strong>
                            <?php echo htmlspecialchars($app['program_name']); ?>
                        </p>
                        <p><strong>Funds Remaining:</strong> $
                            <?php echo number_format($app['funds_remaining'], 2); ?>
                        </p>
                        <hr>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Amount to Release</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required
                                    max="<?php echo $app['funds_remaining']; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="bKash">bKash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Transaction Reference</label>
                                <input type="text" name="transaction_reference" class="form-control"
                                    placeholder="Trx ID / Check No" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Confirm Disbursement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>