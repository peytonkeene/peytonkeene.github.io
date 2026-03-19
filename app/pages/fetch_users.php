<?php
// Include your database connection file
include 'db.php';

// Query to fetch user data
$sql = "SELECT user_id, username FROM users";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['error' => $conn->error]);
    exit();
}

$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'user_id' => $row['user_id'],
            'username' => $row['username'],
        ];
    }
}

echo json_encode($users);

// Close the connection
$conn->close();
?>
