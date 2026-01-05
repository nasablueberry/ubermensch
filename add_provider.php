<?php
require_once('DBconnect.php');
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Add Provider</title></head>
<body>
<?php require_once(__DIR__ . '/inc/header.php'); ?>
<main class="container my-4">
<h1 class="mb-3">Add Aid Provider</h1>
<form method="post" action="insert_provider.php" class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
    <div class="col-md-6"><label class="form-label">Type</label><select class="form-select" name="provider_type"><option>NGO</option><option>Bank</option><option>Govt</option><option>Private Donor</option></select></div>
    <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="contact_email" type="email"></div>
    <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="contact_phone"></div>
    <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address"></div>
    <div class="col-12 text-end"><button class="btn btn-primary" type="submit">Create</button></div>
</form>
<p class="mt-3"><a href="show_providers.php">Back</a></p>
</main>
<?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body></html>