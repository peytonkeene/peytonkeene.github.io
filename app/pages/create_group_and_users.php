<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session and include any necessary files, like your database connection
session_start();
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html"); // Redirect to login if not authorized
    exit();
}

// Function to hash the default password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = $_POST['group_name'];
    $usernames = explode(',', $_POST['usernames']);
    $emails = explode(',', $_POST['emails']);
    $roles = explode(',', $_POST['roles']);

    // Trim any whitespace around usernames, emails, and roles
    $usernames = array_map('trim', $usernames);
    $emails = array_map('trim', $emails);
    $roles = array_map('trim', $roles);

    // Check if the group already exists
    $stmt = $conn->prepare("SELECT group_id FROM `groups` WHERE group_name = ?");
    $stmt->bind_param("s", $group_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Group already exists, fetch the existing group_id
        $stmt->bind_result($group_id);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Create new group
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO `groups` (group_name) VALUES (?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $group_name);
        $stmt->execute();
        $group_id = $stmt->insert_id;
        $stmt->close();
    }

    // Prepare to insert users with their roles
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, group_id, role) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    foreach ($usernames as $index => $username) {
        if (!empty($username) && !empty($emails[$index])) {
            $email = $emails[$index];
            $role = !empty($roles[$index]) ? $roles[$index] : 'user'; // Default to 'user' if no role is provided
            $password_hash = hashPassword("password");
            $stmt->bind_param("sssii", $username, $email, $password_hash, $group_id, $role);
            $stmt->execute();
        }
    }

    $stmt->close();

    // Redirect to a success message
    $success = "Group and users created successfully!";
}

// Fetch users to display
$users = [];
if (isset($group_id)) {
    $stmt = $conn->prepare("SELECT username, email, role FROM users WHERE group_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Setup - MedNarrate</title>
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
            justify-content: center;
            align-items: center;
            height: 200px;
            flex-direction: column;
            position: relative;
            z-index: 10;
        }

        header img {
            height: 120px;
            margin-bottom: 10px;
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
            position: relative;
        }

        .content {
            width: 100%;
            max-width: 800px; /* Adjusted for better button alignment */
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .form-group label {
            font-size: 16px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
            outline: none;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        button {
            padding: 15px 25px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="controller_dashboard.html">Dashboard</a>
            <a href="logout.php" class="logout">Log Out</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <h1>Initial Setup</h1>

            <?php if (isset($success)): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="create_group_and_users.php" method="post">
                <div class="form-group">
                    <label for="group_name">Group Name</label>
                    <input type="text" id="group_name" name="group_name" placeholder="Enter Group Name" required>
                </div>

                <div class="form-group">
                    <label for="usernames">Usernames (comma separated)</label>
                    <textarea id="usernames" name="usernames" placeholder="Enter Usernames (comma separated)" required></textarea>
                </div>

                <div class="form-group">
                    <label for="emails">Emails (comma separated)</label>
                    <textarea id="emails" name="emails" placeholder="Enter Emails (comma separated)" required></textarea>
                </div>

                <div class="form-group">
                    <label for="roles">Roles (comma separated, use 'admin' or 'user')</label>
                    <textarea id="roles" name="roles" placeholder="Enter Roles (comma separated, 'admin' or 'user')" required></textarea>
                </div>

                <button type="submit">Create Group and Users</button>
            </form>
        </div>
    </main>
</body>
</html>
