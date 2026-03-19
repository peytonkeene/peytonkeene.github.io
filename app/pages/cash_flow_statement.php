<?php
session_start();

// Include any necessary files, like your database connection
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html"); // Redirect to login if not authorized
    exit();
}

// Fetch cash flows from invoices paid (Operating Activities)
$paid_invoices = $conn->query("SELECT SUM(amount) AS total_income FROM invoices WHERE status = 'paid'")->fetch_assoc();
$total_income = $paid_invoices['total_income'];

// Fetch actual expenses (Operating Activities)
$actual_expenses = $conn->query("SELECT SUM(amount) AS total_expenses FROM expenses")->fetch_assoc();
$total_expenses = $actual_expenses['total_expenses'];

// Calculate net cash flow from operating activities
$net_cash_from_operating = $total_income - $total_expenses;

// Fetch cash flows from other activities (Investing, Financing)
$investing_cash_flows = $conn->query("SELECT * FROM cash_flows WHERE type = 'investing'")->fetch_all(MYSQLI_ASSOC);
$financing_cash_flows = $conn->query("SELECT * FROM cash_flows WHERE type = 'financing'")->fetch_all(MYSQLI_ASSOC);

$total_investing = array_sum(array_column($investing_cash_flows, 'amount'));
$total_financing = array_sum(array_column($financing_cash_flows, 'amount'));

// Calculate net cash flow
$net_cash_flow = $net_cash_from_operating + $total_investing + $total_financing;

// PDF Generation
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Add Logo
    $pdf->Image('images/logo.png', 10, 6, 30);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40);
    $pdf->Cell(110, 10, 'Cash Flow Statement', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40);
    $pdf->Cell(110, 10, 'As of ' . date('F Y'), 0, 1, 'C');
    $pdf->Ln(20);

    // Operating Activities
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Cash Flow from Operating Activities', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(150, 8, 'Total Income from Paid Invoices', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_income, 2), 1, 1, 'R');
    $pdf->Cell(150, 8, 'Total Actual Expenses', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_expenses, 2), 1, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Net Cash from Operating Activities', 1);
    $pdf->Cell(40, 8, '$' . number_format($net_cash_from_operating, 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Investing Activities
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Cash Flow from Investing Activities', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($investing_cash_flows as $flow) {
        $pdf->Cell(150, 8, $flow['description'], 1);
        $pdf->Cell(40, 8, '$' . number_format($flow['amount'], 2), 1, 1, 'R');
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Net Cash from Investing Activities', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_investing, 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Financing Activities
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Cash Flow from Financing Activities', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($financing_cash_flows as $flow) {
        $pdf->Cell(150, 8, $flow['description'], 1);
        $pdf->Cell(40, 8, '$' . number_format($flow['amount'], 2), 1, 1, 'R');
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Net Cash from Financing Activities', 1);
    $pdf->Cell(40, 8, '$' . number_format($total_financing, 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Net Cash Flow
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(150, 8, 'Net Increase in Cash', 1);
    $pdf->Cell(40, 8, '$' . number_format($net_cash_flow, 2), 1, 1, 'R');

    $pdf->Output('D', 'Cash_Flow_Statement_' . date('F_Y') . '.pdf');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Flow Statement - MedNarrate</title>
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
            <h1>Cash Flow Statement</h1>
            <h2>As of <?php echo date('F Y'); ?></h2>

            <!-- Operating Activities Section -->
            <h3>Cash Flow from Operating Activities</h3>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td>Total Income from Paid Invoices</td>
                    <td>$<?php echo number_format($total_income, 2); ?></td>
                </tr>
                <tr>
                    <td>Total Actual Expenses</td>
                    <td>$<?php echo number_format($total_expenses, 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td>Net Cash from Operating Activities</td>
                    <td>$<?php echo number_format($net_cash_from_operating, 2); ?></td>
                </tr>
            </table>

            <!-- Investing Activities Section -->
            <h3>Cash Flow from Investing Activities</h3>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
                <?php foreach ($investing_cash_flows as $flow): ?>
                <tr>
                    <td><?php echo htmlspecialchars($flow['description']); ?></td>
                    <td>$<?php echo number_format($flow['amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td>Net Cash from Investing Activities</td>
                    <td>$<?php echo number_format($total_investing, 2); ?></td>
                </tr>
            </table>

            <!-- Financing Activities Section -->
            <h3>Cash Flow from Financing Activities</h3>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
                <?php foreach ($financing_cash_flows as $flow): ?>
                <tr>
                    <td><?php echo htmlspecialchars($flow['description']); ?></td>
                    <td>$<?php echo number_format($flow['amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td>Net Cash from Financing Activities</td>
                    <td>$<?php echo number_format($total_financing, 2); ?></td>
                </tr>
            </table>

            <!-- Net Cash Flow -->
            <h3>Net Increase in Cash</h3>
            <table>
                <tr class="total-row">
                    <td>Total Net Cash Flow</td>
                    <td>$<?php echo number_format($net_cash_flow, 2); ?></td>
                </tr>
            </table>

            <!-- Download PDF -->
            <a href="cash_flow_statement.php?download=true" class="btn">Download PDF</a>
        </div>
    </main>
</body>
</html>
