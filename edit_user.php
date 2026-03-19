<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Get the admin's group_id
$admin_group_id = $_SESSION['group_id'];

// Get the username from the URL
$username = $_GET['username'];

// Fetch the user's details if they belong to the same group as the admin
$sql = "SELECT first_name, last_name, username, email, license_number, status FROM users WHERE username = ? AND group_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $username, $admin_group_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $username, $email, $license_number, $status);
$stmt->fetch();
$stmt->close();

// If the user doesn't belong to the admin's group, redirect back to manage users
if (!$username) {
    header("Location: manage_users.php");
    exit();
}

// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_license_number = $_POST['license_number'];
    $new_password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $new_status = $_POST['status'];

    // Update the user's details
    if ($new_password) {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, license_number = ?, password_hash = ?, status = ? WHERE username = ? AND group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $new_first_name, $new_last_name, $new_username, $new_email, $new_license_number, $new_password, $new_status, $username, $admin_group_id);
    } else {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, license_number = ?, status = ? WHERE username = ? AND group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $new_first_name, $new_last_name, $new_username, $new_email, $new_license_number, $new_status, $username, $admin_group_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header styling */
        header {
            background-color: #ffffff;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center; /* Center the content in the header */
            align-items: center;
            height: 200px;
            flex-direction: column; /* Stack logo and navigation */
            position: relative;
            z-index: 10;
        }

        header img {
            height: 120px; /* Adjusted logo size */
            margin-bottom: 10px; /* Space between logo and navigation */
        }

        nav {
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: center;
            z-index: 11;
        }

        nav a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #007bff;
        }

        /* Main content styling */
        main {
            flex: 1;
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: relative;
        }

        .content {
            max-width: 800px;
            margin: 0 auto;
            font-size: 20px;
            line-height: 1.6;
            color: #333;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group button {
            padding: 12px 25px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 18px;
            display: inline-block;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        /* Transparent logo behind the content */
        .background-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 800px;
            opacity: 0.05;
            z-index: 1;
            pointer-events: none;
        }

        /* Footer styling */
        footer {
            background-color: #333;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            margin-top: auto;
            z-index: 10;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #cccccc;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            header {
                height: auto;
            }

            header img {
                height: 100px;
            }

            nav {
                flex-direction: column;
                gap: 10px;
            }

            nav a {
                font-size: 14px;
            }

            .content {
                font-size: 18px;
                padding: 20px;
            }

            .form-group input, .form-group select {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 10px;
                height: auto;
            }

            header img {
                height: 80px;
            }

            nav a {
                font-size: 14px;
            }

            .content {
                font-size: 16px;
                padding: 15px;
            }

            .form-group input, .form-group select {
                font-size: 14px;
            }

            .background-logo {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- Header with logo and navigation -->
    <header>
        <a href="admin_dashboard.html">
            <img src="images/logo.png" alt="MedNarrate Logo">
        </a>
        <nav>
            <a href="admin_dashboard.html">Dashboard</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>

    <!-- Main content area -->
    <main>
        <!-- Background logo behind the content -->
        <img src="images/logo.png" alt="MedNarrate Background Logo" class="background-logo">

        <!-- Main content text -->
        <div class="content">
            <h1>Edit User: <?php echo htmlspecialchars($username); ?></h1>
            <form method="POST">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="license_number">License Number (Optional)</label>
                    <input type="text" id="license_number" name="license_number" value="<?php echo htmlspecialchars($license_number); ?>">
                </div>
                <div class="form-group">
                    <label for="password">New Password (Leave blank to keep current password)</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit">Update User</button>
                </div>
            </form>
            <a href="manage_users.php" class="back-button">Back to Manage Users</a>
        </div>
    </main>

    <!-- Footer with privacy policy and terms of service -->
    <footer>
        <a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a> | &copy; 2024 MedNarrate. All rights reserved.
    </footer>
</body>
</html>
