<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
      rel="stylesheet"
    />
    <title>XYZ School</title>
  </head>
  <body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main>
      <section class="add_student">
        <div class="add_student_box">
          <h1>Add New Student</h1>
          <form class="add_student_form" action="insert_student.php" method="post">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Student ID</label><input class="form-control" type="text" name="student_id" required></div>
              <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
              <div class="col-md-6"><label class="form-label">Birth Certificate ID</label><input class="form-control" type="text" name="birth_certificate_id" required></div>
              <div class="col-12"><label class="form-label">Full Name</label><input class="form-control" type="text" name="name" required></div>
              <div class="col-md-4"><label class="form-label">DOB</label><input class="form-control" type="date" name="dob"></div>
              <div class="col-md-4"><label class="form-label">Gender</label><select class="form-select" name="gender"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select></div>
              <div class="col-md-4"><label class="form-label">Institution ID</label><input class="form-control" type="number" name="institution_id"></div>
              <div class="col-md-4"><label class="form-label">Household Income</label><input class="form-control" type="number" step="0.01" name="household_income"></div>
              <div class="col-md-4"><label class="form-label">GPA</label><input class="form-control" type="number" step="0.01" name="gpa"></div>
              <div class="col-md-4"><label class="form-label">Attendance (%)</label><input class="form-control" type="number" step="0.01" name="attendance"></div>
              <div class="col-md-6"><label class="form-label">Present Address</label><input class="form-control" type="text" name="present_address"></div>
              <div class="col-md-6"><label class="form-label">Permanent Address</label><input class="form-control" type="text" name="permanent_address"></div>
              <div class="col-md-6"><label class="form-label">District</label><input class="form-control" type="text" name="district"></div>
              <div class="col-md-6"><label class="form-label">Upazila</label><input class="form-control" type="text" name="upazila"></div>
              <div class="col-12 text-end"><button type="submit" class="btn btn-primary">Add Student</button></div>
            </div>
          </form>
        </div>
      </section>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
  </body>
</html>
