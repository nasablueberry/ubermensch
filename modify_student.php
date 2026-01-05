<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet" />
  <title>XYZ School</title>
</head>

<body>
  <?php
  require_once(__DIR__ . '/inc/header.php');
  if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'admin') {
    echo "<script>window.location.href='login.php';</script>";
    exit;
  }
  ?>
  <main>
    <section class="add_student">
      <div class="add_student_box">
        <h1>Update Student</h1>
        <?php
        require_once('DBconnect.php');

        if (isset($_GET['id'])) {
          $student_id = (string) $_GET['id'];
          $stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
          $stmt->bind_param('s', $student_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($row = $result->fetch_assoc()) {
            ?>
            <form class="add_student_form" action="update_student.php" method="post">
              <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($row['student_id']); ?>">
              <label>Name</label>
              <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
              <label>Present Address</label>
              <input type="text" name="present_address" value="<?php echo htmlspecialchars($row['present_address']); ?>"
                required>
              <label>Permanent Address</label>
              <input type="text" name="permanent_address"
                value="<?php echo htmlspecialchars($row['permanent_address']); ?>">
              <label>GPA</label>
              <input type="number" step="0.01" name="gpa" value="<?php echo htmlspecialchars($row['gpa']); ?>">
              <label>Attendance (%)</label>
              <input type="number" step="0.01" name="attendance"
                value="<?php echo htmlspecialchars($row['attendance']); ?>">
              <input type="submit" value="Update Student">
            </form>
            <?php
          } else {
            echo "Student not found.";
          }
        } else {
          header("Location: admin_dashboard.php");
        }
        ?>
      </div>
    </section>
  </main>
  <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>