<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user']['student_id'];
$msg = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $income = $_POST['household_income'];
    $address = $_POST['address']; // Combining into one or mapping present
    $district = $_POST['district'];
    $upazila = $_POST['upazila'];

    // Update Query
    $stmt = $conn->prepare("UPDATE Student SET name=?, dob=?, gender=?, household_income=?, address=?, district=?, upazila=? WHERE student_id=?");
    $stmt->bind_param("sssdssss", $name, $dob, $gender, $income, $address, $district, $upazila, $student_id);

    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success">Profile updated successfully!</div>';
        // Refresh session
        $res = $conn->query("SELECT * FROM Student WHERE student_id='$student_id'");
        $_SESSION['user'] = $res->fetch_assoc();
    } else {
        $msg = '<div class="alert alert-danger">Error updating profile: ' . $conn->error . '</div>';
    }
}

$student = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Übermensch</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">My Profile</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $msg; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control"
                                    value="<?php echo htmlspecialchars($student['student_id']); ?>" readonly disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="<?php echo htmlspecialchars($student['name']); ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control"
                                        value="<?php echo htmlspecialchars($student['dob']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="Male" <?php if ($student['gender'] == 'Male')
                                            echo 'selected'; ?>>
                                            Male</option>
                                        <option value="Female" <?php if ($student['gender'] == 'Female')
                                            echo 'selected'; ?>>Female</option>
                                        <option value="Other" <?php if ($student['gender'] == 'Other')
                                            echo 'selected'; ?>>
                                            Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Household Income</label>
                                <input type="number" step="0.01" name="household_income" class="form-control"
                                    value="<?php echo htmlspecialchars($student['household_income']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"
                                    required><?php echo htmlspecialchars($student['address'] ?? $student['present_address'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">District</label>
                                    <input type="text" name="district" class="form-control"
                                        value="<?php echo htmlspecialchars($student['district']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Upazila</label>
                                    <input type="text" name="upazila" class="form-control"
                                        value="<?php echo htmlspecialchars($student['upazila']); ?>" required>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CGPA (Verified by Admin)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($student['cgpa']); ?>" readonly disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Attendance % (Verified by Admin)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($student['attendance_percentage']); ?>"
                                        readonly disabled>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="student_dashboard.php" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary-custom"
                                    style="width:auto; padding:10px 30px;">Save Changes</button>
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