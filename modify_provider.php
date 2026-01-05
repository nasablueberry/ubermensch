<?php
require_once('DBconnect.php');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit;
}
if (!isset($_GET['id'])) {
    header('Location: show_providers.php');
    exit;
}
$id = $_GET['id'];
$stmt = $conn->prepare('SELECT * FROM AidProvider WHERE provider_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo 'Provider not found';
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Provider</title>
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main>
        <h1>Edit Provider</h1>
        <form method="post" action="update_provider.php">
            <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($row['provider_id']); ?>">
            Name: <input name="Name" value="<?php echo htmlspecialchars($row['name']); ?>"><br>
            Type: <input name="provider_type" value="<?php echo htmlspecialchars($row['provider_type']); ?>"><br>
            Email: <input name="contact_email" value="<?php echo htmlspecialchars($row['contact_email']); ?>"><br>
            Phone: <input name="contact_phone" value="<?php echo htmlspecialchars($row['contact_phone']); ?>"><br>
            Address: <input name="address" value="<?php echo htmlspecialchars($row['address']); ?>"><br>
            <input type="submit" value="Save">
        </form>
        <p><a href="show_providers.php">Back</a></p>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>