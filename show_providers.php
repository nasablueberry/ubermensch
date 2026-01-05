<?php
require_once('DBconnect.php');
?>
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Providers</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main class="container my-4">
        <h1>Aid Providers</h1>
        <p><a class="btn btn-sm btn-primary" href="add_provider.php">Add Provider</a></p>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // normalize table name and fetch providers
                    $result = $conn->query("SELECT * FROM aidprovider ORDER BY provider_id DESC");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['provider_id'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['name'] ?? $row['provider_name'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['provider_type'] ?? $row['type'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['contact_email'] ?? $row['email'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['contact_phone'] ?? $row['phone'] ?? '') . '</td>';
                            echo '<td><a href="modify_provider.php?id=' . urlencode($row['provider_id']) . '">Edit</a> | <a href="delete_provider.php?id=' . urlencode($row['provider_id']) . '" onclick="return confirm(\'Delete provider?\')">Delete</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No providers found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>