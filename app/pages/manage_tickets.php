<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'controller') {
    header("Location: login.html");
    exit();
}

// Fetch tickets
$sql = "SELECT * FROM tickets";
$result = $conn->query($sql);

// Check if a comment has been added
$comment_added = isset($_SESSION['comment_added']) ? $_SESSION['comment_added'] : false;
unset($_SESSION['comment_added']);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Support Tickets - MedNarrate</title>
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
            flex-direction: column;
            position: relative;
        }

        .content {
            max-width: 1200px;
            margin: 0 auto;
            font-size: 20px;
            line-height: 1.6;
            color: #333;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed; /* Fixed layout to control column widths */
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px; /* Slightly reduced font size for better fit */
            word-wrap: break-word; /* Ensures text wraps within the cell */
            overflow: hidden;
            text-overflow: ellipsis; /* Adds ellipsis (...) if text is too long */
            white-space: nowrap; /* Prevents text from breaking to new lines */
        }

        th {
            background-color: #007bff;
            color: white;
        }

        th:nth-child(1) { width: 5%; }  /* Ticket ID */
        th:nth-child(2) { width: 15%; } /* Name */
        th:nth-child(3) { width: 20%; } /* Email */
        th:nth-child(4) { width: 20%; } /* Subject */
        th:nth-child(5) { width: 10%; } /* Priority */
        th:nth-child(6) { width: 10%; } /* Status */
        th:nth-child(7) { width: 15%; } /* Comments */
        th:nth-child(8) { width: 10%; }  /* Close Ticket */
        th:nth-child(9) { width: 10%; }  /* See Full Ticket */

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .comment-form {
            margin-top: 5px;
            display: flex;
            flex-direction: column;
        }

        .comment-form textarea {
            width: 100%;
            height: 50px; /* Adjust the height of the textarea */
            margin-bottom: 5px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            resize: none;
            box-sizing: border-box; /* Ensures padding is included in the element's total width and height */
        }

        .button {
            padding: 8px 12px; /* Adjust button padding to fit better */
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .status-message {
            color: green;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        /* Modal styling for full ticket details */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
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

            h1 {
                font-size: 28px;
            }

            table, th, td {
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
                padding: 15px;
            }

            h1 {
                font-size: 24px;
            }

            .background-logo {
                width: 90%;
            }
        }
    </style>
    <script>
        // Function to open the modal
        function openModal(ticketId, name, email, subject, priority, status, message, comments) {
            document.getElementById('ticketDetails').innerHTML = `
                <p><strong>Ticket ID:</strong> ${ticketId}</p>
                <p><strong>Name:</strong> ${name}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Subject:</strong> ${subject}</p>
                <p><strong>Priority:</strong> ${priority}</p>
                <p><strong>Status:</strong> ${status}</p>
                <p><strong>Message:</strong> ${message}</p>
                <p><strong>Comments:</strong> ${comments}</p>
            `;
            document.getElementById('ticketModal').style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('ticketModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <!-- Header with logo and navigation -->
    <header>
        <a href="admin_dashboard.html">
            <img src="images/logo.png" alt="MedNarrate Logo">
        </a>
        <nav>
            <a href="controller_dashboard.html">Dashboard</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>

    <!-- Main content area -->
    <main>
        <h1>Manage Support Tickets</h1>
        <div class="content">
            <?php if ($comment_added): ?>
                <div class="status-message">Comment added to the ticket successfully!</div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Comments</th>
                        <th>Close Ticket</th>
                        <th>See Full Ticket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ticket_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo htmlspecialchars($row['priority']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <form action="update_ticket.php" method="POST" class="comment-form">
                                        <input type="hidden" name="ticket_id" value="<?php echo $row['ticket_id']; ?>">
                                        <textarea name="comments" placeholder="Enter your comments here..." required></textarea>
                                        <button type="submit" name="action" value="comment" class="button">Add Comment</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="update_ticket.php" method="POST">
                                        <input type="hidden" name="ticket_id" value="<?php echo $row['ticket_id']; ?>">
                                        <button type="submit" name="action" value="close" class="button">Close Ticket</button>
                                    </form>
                                </td>
                                <td>
                                    <button class="button" onclick="openModal(
                                        '<?php echo htmlspecialchars($row['ticket_id']); ?>',
                                        '<?php echo htmlspecialchars($row['name']); ?>',
                                        '<?php echo htmlspecialchars($row['email']); ?>',
                                        '<?php echo htmlspecialchars($row['subject']); ?>',
                                        '<?php echo htmlspecialchars($row['priority']); ?>',
                                        '<?php echo htmlspecialchars($row['status']); ?>',
                                        '<?php echo htmlspecialchars($row['message']); ?>',
                                        '<?php echo htmlspecialchars($row['comments']); ?>'
                                    )">See Full Ticket</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No tickets found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal for full ticket details -->
    <div id="ticketModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="ticketDetails"></div>
        </div>
    </div>

    <!-- Footer with privacy policy and terms of service -->
    <footer>
        <a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a> | &copy; 2024 MedNarrate. All rights reserved.
    </footer>
</body>
</html>
