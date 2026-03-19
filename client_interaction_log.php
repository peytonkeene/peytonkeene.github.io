<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and has the role of 'controller'
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3 || $_SESSION['role'] !== 'controller') {
    header("Location: login.html");
    exit();
}

// Fetch clients from the database for selecting during interaction logging
$clientQuery = "SELECT id, name FROM clients";
$clientResult = $conn->query($clientQuery);

if (!$clientResult) {
    die("Query failed: " . $conn->error);
}

$clients = $clientResult->fetch_all(MYSQLI_ASSOC);

// Initialize variables for form validation
$interactionType = $interactionDate = $clientId = $notes = "";
$clientError = $interactionError = $successMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate client selection
    if (empty($_POST["client_id"])) {
        $clientError = "You must select a client.";
    } else {
        $clientId = $_POST["client_id"];
    }

    // Validate interaction type
    if (empty($_POST["interaction_type"])) {
        $interactionError = "Interaction type is required.";
    } else {
        $interactionType = trim($_POST["interaction_type"]);
    }

    // Get interaction date and notes
    $interactionDate = $_POST["interaction_date"];
    $notes = trim($_POST["notes"]);

    // If no errors, insert the interaction into the database
    if (empty($clientError) && empty($interactionError)) {
        $stmt = $conn->prepare("INSERT INTO interactions (client_id, interaction_type, interaction_date, notes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $clientId, $interactionType, $interactionDate, $notes);

        if ($stmt->execute()) {
            $successMessage = "Interaction logged successfully!";
            // Clear form inputs
            $interactionType = $interactionDate = $clientId = $notes = "";
        } else {
            die("Error logging interaction: " . $conn->error);
        }

        $stmt->close();
    }
}

// Fetch all client interactions
$interactionLogQuery = "SELECT interactions.*, clients.name AS client_name FROM interactions JOIN clients ON interactions.client_id = clients.id ORDER BY interaction_date DESC";
$interactionLogResult = $conn->query($interactionLogQuery);

if (!$interactionLogResult) {
    die("Query failed: " . $conn->error);
}

$interactions = $interactionLogResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Interaction Log - MedNarrate CRM</title>
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
            flex-direction: column;
        }

        .content {
            width: 100%;
            max-width: 900px;
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
            cursor: pointer;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        label {
            font-size: 16px;
            text-align: left;
        }

        input[type="text"], input[type="date"], textarea, select {
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

        /* Modal styles for pop-up interaction */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>

    <script>
        // Function to show interaction details in a modal
        function showInteraction(interactionId) {
            var modal = document.getElementById("interactionModal");
            var content = document.getElementById("modalContent");
            var interaction = document.getElementById(interactionId).dataset.details;
            content.innerText = interaction;
            modal.style.display = "block";
        }

        // Function to close modal
        function closeModal() {
            document.getElementById("interactionModal").style.display = "none";
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById("interactionModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
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
            <h1>Client Interaction Log</h1>
            <?php if (!empty($successMessage)): ?>
                <div class="success"><?= $successMessage ?></div>
            <?php endif; ?>

            <!-- Form to log new interactions -->
            <form action="client_interaction_log.php" method="POST">
                <label for="client_id">Select Client:</label>
                <select id="client_id" name="client_id">
                    <option value="">Select a client</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>" <?= ($clientId == $client['id']) ? 'selected' : '' ?>><?= htmlspecialchars($client['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="error"><?= $clientError ?></div>

                <label for="interaction_type">Interaction Type:</label>
                <select id="interaction_type" name="interaction_type">
                    <option value="phone call">Phone Call</option>
                    <option value="text message">Text Message</option>
                    <option value="email">Email</option>
                    <option value="in-person meeting">In-Person Meeting</option>
                    <option value="online meeting">Online Meeting</option>
                    <option value="support ticket">Support Ticket</option>
                    <option value="mail">Mail</option>
                </select>
                <div class="error"><?= $interactionError ?></div>

                <label for="interaction_date">Interaction Date:</label>
                <input type="date" id="interaction_date" name="interaction_date" value="<?= htmlspecialchars($interactionDate) ?>">

                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes"><?= htmlspecialchars($notes) ?></textarea>

                <button type="submit">Log Interaction</button>
            </form>

            <!-- Interaction Log Table -->
            <h2>Interaction History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Interaction Type</th>
                        <th>Interaction Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interactions as $interaction): ?>
                        <tr id="interaction_<?= $interaction['id']; ?>" data-details="<?= htmlspecialchars($interaction['notes']); ?>">
                            <td><?= htmlspecialchars($interaction['client_name']); ?></td>
                            <td><?= htmlspecialchars($interaction['interaction_type']); ?></td>
                            <td><?= htmlspecialchars($interaction['interaction_date']); ?></td>
                            <td><button onclick="showInteraction('interaction_<?= $interaction['id']; ?>')">View Details</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="crm.php" class="back-button">Back to CRM Dashboard</a>
        </div>
    </main>

    <!-- Modal for showing interaction details -->
    <div id="interactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalContent"></p>
        </div>
    </div>
</body>
</html>
