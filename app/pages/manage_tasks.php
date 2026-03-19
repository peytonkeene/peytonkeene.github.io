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

// Fetch tasks from the database
$query = "SELECT tasks.*, clients.name AS client_name FROM tasks JOIN clients ON tasks.client_id = clients.id ORDER BY due_date";
$result = $conn->query($query);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

$tasks = $result->fetch_all(MYSQLI_ASSOC);

// Prepare tasks for the calendar
$calendarTasks = [];
foreach ($tasks as $task) {
    $calendarTasks[] = [
        'title' => $task['task_description'] . ' (Client: ' . $task['client_name'] . ')',
        'start' => $task['due_date'],
        'status' => $task['status']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks - MedNarrate CRM</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
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
            cursor: pointer;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            cursor: pointer;
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

        /* Calendar styling */
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?= json_encode($calendarTasks) ?>
            });
            calendar.render();
        });

        // Function to sort table columns
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("taskTable");
            switching = true;
            // Set the sorting direction to ascending
            dir = "asc"; 
            /* Make a loop that will continue until no switching has been done: */
            while (switching) {
                switching = false;
                rows = table.rows;
                /* Loop through all table rows (except the first, which contains table headers): */
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    /* Get the two elements you want to compare, one from current row and one from the next: */
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    /* Check if the two rows should switch place, based on the direction, asc or desc: */
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            /* If so, mark as a switch and break the loop: */
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            /* If so, mark as a switch and break the loop: */
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    /* If a switch has been marked, make the switch and mark that a switch has been done: */
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    // Each time a switch is done, increase this count by 1:
                    switchcount++;      
                } else {
                    /* If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again. */
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
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
            <h1>Manage Tasks</h1>
            <div id="calendar"></div> <!-- Calendar Container -->

            <!-- Task Manager Table -->
            <h2>Task List</h2>
            <table id="taskTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Client Name</th>
                        <th onclick="sortTable(1)">Task Description</th>
                        <th onclick="sortTable(2)">Assigned To</th>
                        <th onclick="sortTable(3)">Due Date</th>
                        <th onclick="sortTable(4)">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['client_name']); ?></td>
                            <td><?= htmlspecialchars($task['task_description']); ?></td>
                            <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                            <td><?= htmlspecialchars($task['due_date']); ?></td>
                            <td><?= htmlspecialchars($task['status']); ?></td>
                            <td>
                                <div class="button-group">
                                    <a href="edit_task.php?id=<?= $task['id']; ?>" class="button">Edit</a>
                                    <form action="delete_task.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $task['id']; ?>">
                                        <button type="submit" class="button">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="add_task.php" class="button">Add New Task</a>
            <a href="crm.php" class="back-button">Back to CRM Dashboard</a>
        </div>
    </main>
</body>
</html>
