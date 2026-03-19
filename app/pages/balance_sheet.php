<?php
session_start();

// Include any necessary files, like your database connection
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html"); // Redirect to login if not authorized
    exit();
}

// Handle Add Asset or Liability
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $type = $_POST['type']; // assets or liabilities
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "INSERT INTO $type (description, amount) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $description, $amount);
    $stmt->execute();
    $stmt->close();
}

// Handle Edit Asset or Liability
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_item'])) {
    $type = $_POST['type']; // assets or liabilities
    $id = $_POST['id'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "UPDATE $type SET description = ?, amount = ? WHERE {$type}_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdi", $description, $amount, $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Asset or Liability
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $type = $_POST['type']; // assets or liabilities
    $id = $_POST['id'];

    $sql = "DELETE FROM $type WHERE {$type}_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch assets and liabilities
$assets = $conn->query("SELECT * FROM assets")->fetch_all(MYSQLI_ASSOC);
$liabilities = $conn->query("SELECT * FROM liabilities")->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$total_assets = array_sum(array_column($assets, 'amount'));
$total_liabilities = array_sum(array_column($liabilities, 'amount'));
$total_equity = $total_assets - $total_liabilities;

// PDF Generation
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Add Logo
    $pdf->Image('images/logo.png', 10, 6, 30);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40);
    $pdf->Cell(110, 10, 'Balance Sheet', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40);
    $pdf->Cell(110, 10, 'As of ' . date('F Y'), 0, 1, 'C');
    $pdf->Ln(20);

    // Assets Section
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Assets', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($assets as $asset) {
        $pdf->Cell(150, 8, $asset['description'], 1);
        $pdf->Cell(40, 8, '$' . number_format($asset['amount'], 2), 1, 1, 'R');
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Total Assets', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_assets, 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Liabilities Section
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Liabilities', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($liabilities as $liability) {
        $pdf->Cell(150, 8, $liability['description'], 1);
        $pdf->Cell(40, 8, '$' . number_format($liability['amount'], 2), 1, 1, 'R');
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Total Liabilities', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_liabilities, 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Equity Section
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Equity', 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Total Equity', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_equity, 2), 1, 1, 'R');

    $pdf->Output('D', 'Balance_Sheet_' . date('F_Y') . '.pdf');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet - MedNarrate</title>
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
            max-width: 800px;
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

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .total-row {
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease;
            margin: 10px 0;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #218838;
        }

        .actions {
            text-align: right;
        }

        .actions button {
            margin-left: 5px;
            padding: 8px 12px;
            font-size: 14px;
            background-color: #ffc107;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions button:hover {
            background-color: #e0a800;
        }

        .actions form {
            display: inline;
        }
    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="manage_finances.php">Manage Finances</a>
            <a href="logout.php" class="logout">Log Out</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <h1>Balance Sheet</h1>
            <h2>As of <?php echo date('F Y'); ?></h2>

            <!-- Assets Section -->
            <h3>Assets</h3>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($assets as $asset): ?>
                <tr>
                    <td><?php echo htmlspecialchars($asset['description']); ?></td>
                    <td>$<?php echo number_format($asset['amount'], 2); ?></td>
                    <td class="actions">
                        <form action="balance_sheet.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $asset['asset_id']; ?>">
                            <input type="hidden" name="type" value="assets">
                            <button type="submit" name="edit_item">Edit</button>
                        </form>
                        <form action="balance_sheet.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $asset['asset_id']; ?>">
                            <input type="hidden" name="type" value="assets">
                            <button type="submit" name="delete_item" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td>Total Assets</td>
                    <td>$<?php echo number_format($total_assets, 2); ?></td>
                    <td></td>
                </tr>
            </table>

            <!-- Liabilities Section -->
            <h3>Liabilities</h3>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($liabilities as $liability): ?>
                <tr>
                    <td><?php echo htmlspecialchars($liability['description']); ?></td>
                    <td>$<?php echo number_format($liability['amount'], 2); ?></td>
                    <td class="actions">
                        <form action="balance_sheet.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $liability['liability_id']; ?>">
                            <input type="hidden" name="type" value="liabilities">
                            <button type="submit" name="edit_item">Edit</button>
                        </form>
                        <form action="balance_sheet.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $liability['liability_id']; ?>">
                            <input type="hidden" name="type" value="liabilities">
                            <button type="submit" name="delete_item" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td>Total Liabilities</td>
                    <td>$<?php echo number_format($total_liabilities, 2); ?></td>
                    <td></td>
                </tr>
            </table>

            <!-- Equity Section -->
            <h3>Equity</h3>
            <table>
                <tr class="total-row">
                    <td>Total Equity</td>
                    <td>$<?php echo number_format($total_equity, 2); ?></td>
                </tr>
            </table>

            <!-- Add Asset or Liability Form -->
            <h3>Add Asset or Liability</h3>
            <form action="balance_sheet.php" method="post" class="form-group">
                <label for="type">Type:</label>
                <select name="type" id="type" required>
                    <option value="assets">Asset</option>
                    <option value="liabilities">Liability</option>
                </select><br>

                <label for="description">Description:</label>
                <input type="text" name="description" id="description" required><br>

                <label for="amount">Amount:</label>
                <input type="number" step="0.01" name="amount" id="amount" required><br>

                <button type="submit" name="add_item">Add Item</button>
            </form>

            <!-- Download PDF -->
            <a href="balance_sheet.php?download=true" class="btn">Download PDF</a>
        </div>
    </main>
</body>
</html>
