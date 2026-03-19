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

// Fetch clients from the database using mysqli
$query = "SELECT * FROM clients";
$result = $conn->query($query);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

$clients = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients - MedNarrate CRM</title>
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
            max-width: 900px; /* Adjusted width for client management */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button:hover {
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

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this client? This action cannot be undone.');
        }
    </script>
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
            <h1>Manage Clients</h1>

            <h2>Possible Clients</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <?php if ($client['status'] == 'possible'): ?>
                            <tr>
                                <td><?= htmlspecialchars($client['name']); ?></td>
                                <td><?= htmlspecialchars($client['email']); ?></td>
                                <td><?= htmlspecialchars($client['phone']); ?></td>
                                <td>
                                    <div class="button-group">
                                        <a href="edit_client.php?id=<?= $client['id']; ?>" class="button">Edit</a>
                                        <form action="delete_client.php" method="POST" onsubmit="return confirmDelete();" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $client['id']; ?>">
                                            <button type="submit" class="button">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Existing Clients</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <?php if ($client['status'] == 'existing'): ?>
                            <tr>
                                <td><?= htmlspecialchars($client['name']); ?></td>
                                <td><?= htmlspecialchars($client['email']); ?></td>
                                <td><?= htmlspecialchars($client['phone']); ?></td>
                                <td>
                                    <div class="button-group">
                                        <a href="edit_client.php?id=<?= $client['id']; ?>" class="button">Edit</a>
                                        <form action="delete_client.php" method="POST" onsubmit="return confirmDelete();" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $client['id']; ?>">
                                            <button type="submit" class="button">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="add_client.php" class="button">Add New Client</a>
            <a href="crm.php" class="back-button">Back to CRM Dashboard</a>
        </div>
    </main>
</body>
</html>
