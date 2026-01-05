<?php
require_once('DBconnect.php');
if (!session_id())
    session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Scholarship Programs</title>
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main class="container my-4">
        <h1>Scholarship / Aid Programs</h1>
        <p><a class="btn btn-sm btn-primary" href="add_program.php">Create Program</a> <a
                class="btn btn-sm btn-outline-secondary ms-2" href="show_providers.php">Providers</a></p>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Provider</th>
                        <th>Total Funds</th>
                        <th>Remaining</th>
                        <th>Period</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // detect provider name column (handles differing schemas)
                    $colsRes = $conn->query("DESCRIBE aidprovider");
                    if (!$colsRes) {
                        $colsRes = $conn->query("DESCRIBE AidProvider");
                    }
                    $providerNameCol = null;
                    if ($colsRes) {
                        $fields = [];
                        while ($c = $colsRes->fetch_assoc())
                            $fields[] = $c['Field'];
                        $candidates = ['name', 'provider_name', 'company_name', 'org_name', 'provider_fullname', 'title'];
                        foreach ($candidates as $cand)
                            if (in_array($cand, $fields)) {
                                $providerNameCol = $cand;
                                break;
                            }
                    }

                    if ($providerNameCol) {
                        $sql = "SELECT sp.*, ap." . $providerNameCol . " AS provider_name FROM scholarshipprogram sp LEFT JOIN aidprovider ap ON sp.provider_id = ap.provider_id";

                        // Filter for "My Programs"
                        if (isset($_GET['my_programs']) && isset($_SESSION['user']) && $_SESSION['user_type'] == 'provider') {
                            $pid = $_SESSION['user']['provider_id'];
                            $sql .= " WHERE sp.provider_id = $pid";
                        }

                        $sql .= " ORDER BY sp.program_id DESC";
                    } else {
                        // fallback: select programs without provider name
                        $sql = "SELECT sp.* FROM scholarshipprogram sp ORDER BY sp.program_id DESC";
                    }
                    $res = $conn->query($sql);
                    if ($res && $res->num_rows > 0) {
                        while ($r = $res->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($r['program_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['program_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['provider_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['total_funds']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['funds_remaining']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['start_date']) . ' - ' . htmlspecialchars($r['end_date']) . '</td>';
                            echo '<td><a href="apply_program.php?id=' . urlencode($r['program_id']) . '">Apply</a> | <a href="modify_program.php?id=' . urlencode($r['program_id']) . '">Edit</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">No programs</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <?php require_once(__DIR__ . '/inc/footer.php'); ?>
    </main>
</body>

</html>