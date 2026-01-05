<?php
require_once('DBconnect.php');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Übermensch – Students List</title>
    <style>
        /* minimal page-specific styles retained */
        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #0d3b66;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f7f7f7
        }
    </style>
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main>
        <h1>Students List</h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <?php
                // Inspect actual student columns and only select/display those that exist
                $colsRes = $conn->query("DESCRIBE student");
                if (!$colsRes) {
                    $colsRes = $conn->query("DESCRIBE Student");
                }
                $fields = [];
                if ($colsRes) {
                    while ($c = $colsRes->fetch_assoc())
                        $fields[] = $c['Field'];
                }

                $mapping = [
                    'student_id' => 'Student ID',
                    'name' => 'Name',
                    'DOB' => 'DOB',
                    'gender' => 'Gender',
                    'gpa' => 'GPA',
                    'attendance' => 'Attendance (%)',
                    'household_income' => 'Household Income',
                    'present_address' => 'Present Address',
                    'permanent_address' => 'Permanent Address',
                    'district' => 'District',
                    'upazila' => 'Upazila',
                    'verification_status' => 'Verification Status',
                    'institution_id' => 'Institution ID'
                ];

                $displayCols = [];
                $headers = [];
                foreach ($mapping as $col => $label) {
                    if (in_array($col, $fields)) {
                        $displayCols[] = $col;
                        $headers[] = $label;
                    }
                }

                // render header
                echo '<thead><tr>';
                foreach ($headers as $h)
                    echo '<th>' . htmlspecialchars($h) . '</th>';
                echo '</tr></thead>';

                // if no display columns, show message
                if (empty($displayCols)) {
                    echo '<tbody><tr><td>No student columns found in DB.</td></tr></tbody>';
                } else {
                    // fetch rows selecting only existing columns
                    $sql = 'SELECT ' . implode(', ', $displayCols) . ' FROM student ORDER BY student_id DESC';
                    $result = $conn->query($sql);
                    echo '<tbody>';
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            foreach ($displayCols as $col) {
                                echo '<td>' . htmlspecialchars($row[$col] ?? '') . '</td>';
                            }
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="' . count($displayCols) . '" class="text-center">No students found.</td></tr>';
                    }
                    echo '</tbody>';
                }
                ?>
            </table>
        </div>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>