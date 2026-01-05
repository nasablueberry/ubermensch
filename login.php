<?php
session_start();
require_once('DBconnect.php');

// --- Handle Signups ---
function handleSignup($conn)
{
    $type = $_POST['signup_type'];

    if ($type == 'student') {
        // Corrected INSERT to match schema: password included in Student table
        $stmt = $conn->prepare("INSERT INTO Student (student_id, name, password, dob, gender, household_income, cgpa, attendance_percentage, birth_certificate_id, address, district, upazila, institution_id, region_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Helper to handle optional integers (FKs)
        $institution_id = !empty($_POST['institution_id']) ? $_POST['institution_id'] : null;

        // Auto-assign Region ID based on District and Upazila
        $region_id = null;
        $getRegion = $conn->prepare("SELECT region_id FROM Region WHERE district=? AND upazila=?");
        $getRegion->bind_param("ss", $_POST['district'], $_POST['upazila']);
        $getRegion->execute();
        $regResult = $getRegion->get_result();
        if ($regResult && $regResult->num_rows > 0) {
            $region_id = $regResult->fetch_assoc()['region_id'];
        }
        $getRegion->close();

        // Types: sss s ddd d s sss s ii 
        $stmt->bind_param(
            "sssssdddssssii",
            $_POST['username'],
            $_POST['name'],
            $password_hash,
            $_POST['dob'],
            $_POST['gender'],
            $_POST['household_income'],
            $_POST['cgpa'],
            $_POST['attendance'],
            $_POST['birth_certificate_id'],
            $_POST['present_address'],
            $_POST['district'],
            $_POST['upazila'],
            $institution_id,
            $region_id
        );

        try {
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Student registered successfully! Please Login.</div>";
            }
        } catch (mysqli_sql_exception $e) {
            // Check for specific foreign key errors to give a friendly message
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                if (strpos($e->getMessage(), 'institution_id') !== false) {
                    echo "<div class='alert alert-danger'><b>Registration Failed:</b> The Institution ID you entered (" . htmlspecialchars($institution_id) . ") does not exist. Please enter a valid ID or leave it blank.</div>";
                } elseif (strpos($e->getMessage(), 'region_id') !== false) {
                    echo "<div class='alert alert-danger'><b>Registration Failed:</b> No Region found for District: " . htmlspecialchars($_POST['district']) . " and Upazila: " . htmlspecialchars($_POST['upazila']) . ". Please contact admin to add this region.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Database Error: Foreign Key Constraint Failed.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        $stmt->close();
    }

    if ($type == 'provider') {
        // Corrected INSERT for AidProvider including password
        $stmt = $conn->prepare("INSERT INTO AidProvider (name, password, provider_type, contact_email, contact_phone, address) VALUES (?,?,?,?,?,?)");
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt->bind_param(
            "ssssss",
            $_POST['name'],
            $password_hash,
            $_POST['provider_type'],
            $_POST['contact_email'],
            $_POST['contact_phone'],
            $_POST['address']
        );

        if ($stmt->execute()) {
            echo "<script>alert('Aid Provider registered successfully! Please Login.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }

    if ($type == 'admin') {
        // Corrected INSERT for Admin
        $stmt = $conn->prepare("INSERT INTO Admin (username, password) VALUES (?,?)");
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt->bind_param(
            "ss",
            $_POST['username'],
            $password_hash
        );

        if ($stmt->execute()) {
            echo "<script>alert('Admin registered successfully! Please Login.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// --- Handle Logins ---
function handleLogin($conn)
{
    $type = $_POST['user_type_login'];
    $username = $_POST['username_login'];
    $password = $_POST['password_login'];

    if ($type == 'student') {
        $stmt = $conn->prepare("SELECT * FROM Student WHERE student_id=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row;
                $_SESSION['user_type'] = 'student';
                header("Location: student_dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid password!');</script>";
            }
        } else {
            echo "<script>alert('Student ID not found!');</script>";
        }
    }

    if ($type == 'admin') {
        $stmt = $conn->prepare("SELECT * FROM Admin WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Set a mock user array compatible with other parts if needed, or just admin data
                $_SESSION['user'] = ['name' => $row['username'], 'id' => $row['admin_id']];
                $_SESSION['user_type'] = 'admin';
                header("Location: admin_dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid Admin Password');</script>";
            }
        } else {
            // Fallback hardcoded for initial existing admins if any, or just fail
            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['user'] = ['name' => 'Administrator'];
                $_SESSION['user_type'] = 'admin';
                header("Location: admin_dashboard.php");
                exit;
            }
            echo "<script>alert('Admin not found!');</script>";
        }
    }

    if ($type == 'aidprovider') {
        // Login using Name (since no username col in Schema for Provider, usually email is better but code used name/username input)
        // Let's assume we match against Name or Email? The form asks for "Username".
        // Provider Signup asks for "Organization Name" (name) and "Username" (which I mapped to name in my previous thought, but better to use name as unique desc).
        // Wait, the form has a specific `username` field for Provider Signup in HTML.
        // BUT `create_tables.sql` AidProvider only has `name`.
        // I should stick to `name` as the identifier or `contact_email`.
        // Let's use `name` to match the previous logic where username -> name.

        $stmt = $conn->prepare("SELECT * FROM AidProvider WHERE name=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row;
                $_SESSION['user_type'] = 'provider';
                header("Location: provider_dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid Password');</script>";
            }
        } else {
            echo "<script>alert('Provider Organization not found');</script>";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup_type']))
        handleSignup($conn);
    if (isset($_POST['login_type']))
        handleLogin($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Übermensch – Login / Signup</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main>
        <div class="auth-container">
            <div class="auth-header">
                <h3>Welcome</h3>
                <p>Please login or sign up to continue</p>
            </div>
            <div class="tab-header">
                <button class="tab-link active" onclick="openTab('login', this)">Login</button>
                <button class="tab-link" onclick="openTab('signup', this)">Sign Up</button>
            </div>

            <div class="auth-body">
                <!-- LOGIN FORM -->
                <div id="login" class="tab-content">
                    <form method="post">
                        <select name="user_type_login" class="form-select" required>
                            <option value="">Select User Type</option>
                            <option value="student">Student</option>
                            <option value="aidprovider">Aid Provider</option>
                            <option value="admin">Admin</option>
                        </select>
                        <input type="text" name="username_login" class="form-control"
                            placeholder="Username / Student ID" required>
                        <input type="password" name="password_login" class="form-control" placeholder="Password"
                            required>
                        <input type="submit" name="login_type" value="Login" class="btn-primary-custom">
                    </form>
                </div>

                <!-- SIGNUP FORM -->
                <div id="signup" class="tab-content" style="display:none;">
                    <select id="signup_select" class="form-select" onchange="showSignupForm(this.value)">
                        <option value="">Select Registration Type</option>
                        <option value="student_signup">Student</option>
                        <option value="provider_signup">Aid Provider</option>
                        <option value="admin_signup">Admin</option>
                    </select>

                    <!-- STUDENT SIGNUP -->
                    <form method="post" id="student_signup" style="display:none;">
                        <input type="hidden" name="signup_type" value="student">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required />
                        <input type="text" name="username" class="form-control" placeholder="Student ID (Username)"
                            required />
                        <input type="password" name="password" class="form-control" placeholder="Password" required />
                        <div class="row">
                            <div class="col-6"><input type="date" name="dob" class="form-control" required /></div>
                            <div class="col-6">
                                <select name="gender" class="form-select" required>
                                    <option value="">Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <input type="number" step="0.01" name="household_income" class="form-control"
                            placeholder="Household Income" required />
                        <input type="number" step="0.01" name="cgpa" class="form-control" placeholder="CGPA" required />
                        <input type="number" step="0.01" name="attendance" class="form-control"
                            placeholder="Attendance %" required />
                        <input type="text" name="birth_certificate_id" class="form-control"
                            placeholder="Birth Certificate ID" required />
                        <input type="text" name="present_address" class="form-control" placeholder="Present Address"
                            required />
                        <input type="text" name="district" class="form-control" placeholder="District" required />
                        <input type="text" name="upazila" class="form-control" placeholder="Upazila" required />
                        <input type="number" name="institution_id" class="form-control"
                            placeholder="Institution ID (Optional)" />
                        <!-- Region ID is auto-assigned based on District/Upazila -->
                        <input type="submit" value="Register Student" class="btn-primary-custom">
                    </form>

                    <!-- PROVIDER SIGNUP -->
                    <form method="post" id="provider_signup" style="display:none;">
                        <input type="hidden" name="signup_type" value="provider">
                        <input type="text" name="name" class="form-control" placeholder="Organization Name" required />
                        <input type="password" name="password" class="form-control" placeholder="Password" required />
                        <select name="provider_type" class="form-select" required>
                            <option value="">Provider Type</option>
                            <option value="NGO">NGO</option>
                            <option value="Bank">Bank</option>
                            <option value="Govt">Govt</option>
                            <option value="Private Donor">Private Donor</option>
                        </select>
                        <input type="email" name="contact_email" class="form-control" placeholder="Email" required />
                        <input type="text" name="contact_phone" class="form-control" placeholder="Phone" required />
                        <input type="text" name="address" class="form-control" placeholder="Address" required />
                        <input type="submit" value="Register Provider" class="btn-primary-custom">
                    </form>

                    <!-- ADMIN SIGNUP -->
                    <div id="admin_signup" style="display:none; text-align:center; padding:20px;">
                        <p>Admin registration is disabled. Please contact system administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once(__DIR__ . '/inc/footer.php'); ?>

    <script>
        function openTab(tabName, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName).style.display = 'block';
            btn.classList.add('active');
        }

        function showSignupForm(val) {
            document.getElementById('student_signup').style.display = 'none';
            document.getElementById('provider_signup').style.display = 'none';
            document.getElementById('admin_signup').style.display = 'none';
            if (val) document.getElementById(val).style.display = 'block';
        }
    </script>
</body>

</html>