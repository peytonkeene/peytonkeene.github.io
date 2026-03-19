<?php
// Start the session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if the user has the 'admin' role, 'controller' role, or belongs to the 'controller' group
$role = $_SESSION['role'];
$group_id = $_SESSION['group_id'];

// Define the controller group ID
$controller_group_id = 3;

if ($role !== 'admin' && $role !== 'controller' && $group_id !== $controller_group_id) {
    header("Location: login.html");
    exit();
}

// Fetch all groups
$groups = [];
$group_query = "SELECT * FROM groups";
$group_result = $conn->query($group_query);
while ($row = $group_result->fetch_assoc()) {
    $groups[] = $row;
}

// Fetch all narrative generators
$generators = [];
$generator_query = "SELECT * FROM narrative_generators";
$generator_result = $conn->query($generator_query);
while ($row = $generator_result->fetch_assoc()) {
    $generators[] = $row;
}

// Fetch existing group-to-generator mappings
$group_generators = [];
$mapping_query = "SELECT * FROM group_generators";
$mapping_result = $conn->query($mapping_query);
while ($row = $mapping_result->fetch_assoc()) {
    $group_generators[$row['group_id']][] = $row['generator_id'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clear existing mappings
    $conn->query("DELETE FROM group_generators");

    // Insert new mappings
    foreach ($_POST['group'] as $group_id => $generator_ids) {
        foreach ($generator_ids as $generator_id) {
            $stmt = $conn->prepare("INSERT INTO group_generators (group_id, generator_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $group_id, $generator_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect to the controller dashboard after updating assignments
    header("Location: controller_dashboard.html?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Management - Narrative Generators</title>
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

        .content {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .group {
            margin-bottom: 20px;
        }

        .group h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .generator {
            margin-bottom: 10px;
            display: inline-block;
        }

        .generator label {
            display: inline-block;
            padding: 10px 20px;
            background-color: rgba(0, 123, 255, 0.7); /* Bubble-like button */
            color: white;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .generator label:hover {
            background-color: rgba(0, 86, 179, 0.7); /* Darker blue on hover */
        }

        .generator input[type="checkbox"] {
            display: none;
        }

        .generator input[type="checkbox"]:checked + label {
            background-color: rgba(0, 55, 130, 0.9); /* Darker blue when selected */
        }

        .submit-button {
            padding: 15px 25px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            text-align: center;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            font-size: 18px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Group Management - Narrative Generators</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Narrative generators updated successfully!</div>
        <?php endif; ?>

        <form action="group_management.php" method="post">
            <?php foreach ($groups as $group): ?>
                <div class="group">
                    <h2><?php echo htmlspecialchars($group['group_name']); ?></h2>
                    <?php foreach ($generators as $generator): ?>
                        <div class="generator">
                            <input type="checkbox" name="group[<?php echo $group['group_id']; ?>][]" value="<?php echo $generator['generator_id']; ?>" id="generator-<?php echo $group['group_id'] . '-' . $generator['generator_id']; ?>"
                                <?php
                                // Check if this generator is already assigned to the group
                                if (isset($group_generators[$group['group_id']]) && in_array($generator['generator_id'], $group_generators[$group['group_id']])) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <label for="generator-<?php echo $group['group_id'] . '-' . $generator['generator_id']; ?>">
                                <?php echo htmlspecialchars($generator['generator_name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="submit-button">Update Assignments</button>
        </form>
    </div>
</body>
</html>
