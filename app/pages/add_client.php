<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in, has a group_id of 3, and has the role of 'controller'
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3 || $_SESSION['role'] !== 'controller') {
    header("Location: login.html"); // Redirect to login page if not authorized
    exit();
}

// Initialize variables for form validation
$clientName = $clientEmail = $clientPhone = $clientStatus = "";
$nameError = $emailError = $successMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate client name
    if (empty($_POST["name"])) {
        $nameError = "Client name is required.";
    } else {
        $clientName = trim($_POST["name"]);
    }

    // Validate client email
    if (empty($_POST["email"])) {
        $emailError = "Client email is required.";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
    } else {
        $clientEmail = trim($_POST["email"]);
    }

    // Get client phone (optional)
    $clientPhone = trim($_POST["phone"]);

    // Get client status (possible or existing)
    $clientStatus = isset($_POST["status"]) ? trim($_POST["status"]) : 'possible';

    // If no errors, insert client into the database
    if (empty($nameError) && empty($emailError)) {
        $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $clientName, $clientEmail, $clientPhone, $clientStatus);

        if ($stmt->execute()) {
            $successMessage = "Client added successfully!";
            // Clear form inputs
            $clientName = $clientEmail = $clientPhone = $clientStatus = "";
        } else {
            die("Error adding client: " . $conn->error);
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Client - MedNarrate CRM</title>
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
            max-width: 600px;
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
            gap: 15px;
        }

        label {
            font-size: 16px;
            text-align: left;
        }

        input[type="text"], input[type="email"], select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: left;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
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

        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
        }

    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="logout.php" class="logout">Log Out</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <h1>Add New Client</h1>
            <?php if (!empty($successMessage)): ?>
                <div class="success"><?= $successMessage ?></div>
            <?php endif; ?>
            <form action="add_client.php" method="POST">
                <label for="name">Client Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($clientName) ?>">
                <div class="error"><?= $nameError ?></div>

                <label for="email">Client Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($clientEmail) ?>">
                <div class="error"><?= $emailError ?></div>

                <label for="phone">Client Phone (optional):</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($clientPhone) ?>">

                <label for="status">Client Status:</label>
                <select id="status" name="status">
                    <option value="possible" <?= ($clientStatus == 'possible') ? 'selected' : ''; ?>>Possible Client</option>
                    <option value="existing" <?= ($clientStatus == 'existing') ? 'selected' : ''; ?>>Existing Client</option>
                </select>

                <button type="submit">Add Client</button>
            </form>
            <a href="manage_clients.php" class="back-button">Back to Manage Clients</a>
        </div>
    </main>
</body>
</html>
