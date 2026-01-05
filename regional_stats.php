<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ensure the stats are up to date (simple aggregate query)
// We will aggregate data from Application/Disbursement/Student tables dynamically
// Group by District (Region)

$query = "
    SELECT 
        s.district,
        COUNT(DISTINCT s.student_id) as total_students,
        COUNT(DISTINCT CASE WHEN a.status='approved' THEN a.student_id END) as supported_students,
        COALESCE(SUM(d.amount_released), 0) as total_disbursed
    FROM Student s
    LEFT JOIN Application a ON s.student_id = a.student_id
    LEFT JOIN Disbursement d ON a.application_id = d.application_id
    GROUP BY s.district
    ORDER BY total_disbursed DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Regional Impact Analysis - Übermensch</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main class="container my-5">
        <h1 class="mb-4">Regional Impact Analysis</h1>

        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <canvas id="impactChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="custom-table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>District</th>
                        <th>Total Registered Students</th>
                        <th>Supported Students (Approved)</th>
                        <th>Total Funds Disbursed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $labels = [];
                    $data_funds = [];
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $labels[] = $row['district'];
                            $data_funds[] = $row['total_disbursed'];
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($row['district']); ?>
                                </td>
                                <td>
                                    <?php echo $row['total_students']; ?>
                                </td>
                                <td>
                                    <?php echo $row['supported_students']; ?>
                                </td>
                                <td>$
                                    <?php echo number_format($row['total_disbursed'], 2); ?>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>

    <script>
        const ctx = document.getElementById('impactChart').getContext('2d');
        const impactChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Total Funds Disbursed ($)',
                data: <?php echo json_encode($data_funds); ?>,
                backgroundColor: 'rgba(0, 119, 204, 0.6)',
                borderColor: 'rgba(0, 119, 204, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>
</body>

</html>