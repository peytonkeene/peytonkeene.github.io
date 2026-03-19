<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html");
    exit();
}

$current_year = date('Y');

// Initialize arrays to store monthly data
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

$expected_incomes = [];
$expected_expenses = [];
$actual_incomes = [];
$actual_expenses = [];

$total_expected_income_year = 0;
$total_expected_expenses_year = 0;
$total_actual_income_ytd = 0;
$total_actual_expenses_ytd = 0;

foreach ($months as $month_num => $month_name) {
    $expected_income_query = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM invoices WHERE MONTH(date_due) = $month_num AND YEAR(date_due) = $current_year AND status = 'pending'");
    $expected_expense_query = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM expected_expenses WHERE MONTH(date) = $month_num AND YEAR(date) = $current_year");
    $actual_income_query = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM invoices WHERE MONTH(date_due) = $month_num AND YEAR(date_due) = $current_year AND status = 'paid'");
    $actual_expense_query = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE MONTH(date) = $month_num AND YEAR(date) = $current_year");

    $expected_income = $expected_income_query->fetch_assoc()['total'];
    $expected_expense = $expected_expense_query->fetch_assoc()['total'];
    $actual_income = $actual_income_query->fetch_assoc()['total'];
    $actual_expense = $actual_expense_query->fetch_assoc()['total'];

    $expected_incomes[$month_name] = $expected_income;
    $expected_expenses[$month_name] = $expected_expense;
    $actual_incomes[$month_name] = $actual_income;
    $actual_expenses[$month_name] = $actual_expense;

    $total_expected_income_year += $expected_income;
    $total_expected_expenses_year += $expected_expense;
    $total_actual_income_ytd += $actual_income;
    $total_actual_expenses_ytd += $actual_expense;
}

// Generate PDF if requested
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Add Logo
    $pdf->Image('images/logo.png', 10, 6, 30);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40);
    $pdf->Cell(110, 10, 'Budget Report', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40);
    $pdf->Cell(110, 10, 'For the Year ' . $current_year, 0, 1, 'C');
    $pdf->Ln(20);

    // Monthly Budget Table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Monthly Budget', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 8, 'Month', 1);
    $pdf->Cell(50, 8, 'Expected Income', 1);
    $pdf->Cell(50, 8, 'Expected Expenses', 1);
    $pdf->Cell(50, 8, 'Net Income (Expected)', 1, 1);

    foreach ($months as $month_name) {
        $expected_income = $expected_incomes[$month_name];
        $expected_expense = $expected_expenses[$month_name];
        $net_income_expected = $expected_income - $expected_expense;

        $pdf->Cell(40, 8, $month_name, 1);
        $pdf->Cell(50, 8, '$' . number_format($expected_income, 2), 1);
        $pdf->Cell(50, 8, '$' . number_format($expected_expense, 2), 1);
        $pdf->Cell(50, 8, '$' . number_format($net_income_expected, 2), 1, 1);
    }
    $pdf->Ln(10);

    // Year-to-Date Actual Table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Year-to-Date (YTD) Budget', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 8, 'Month', 1);
    $pdf->Cell(50, 8, 'Actual Income', 1);
    $pdf->Cell(50, 8, 'Actual Expenses', 1);
    $pdf->Cell(50, 8, 'Net Income (Actual)', 1, 1);

    foreach ($months as $month_name) {
        $actual_income = $actual_incomes[$month_name];
        $actual_expense = $actual_expenses[$month_name];
        $net_income_actual = $actual_income - $actual_expense;

        $pdf->Cell(40, 8, $month_name, 1);
        $pdf->Cell(50, 8, '$' . number_format($actual_income, 2), 1);
        $pdf->Cell(50, 8, '$' . number_format($actual_expense, 2), 1);
        $pdf->Cell(50, 8, '$' . number_format($net_income_actual, 2), 1, 1);
    }
    $pdf->Ln(10);

    // Year-End Expected Summary
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Year-End Budget Summary', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(100, 8, 'Expected Year Income', 1);
    $pdf->Cell(90, 8, '$' . number_format($total_expected_income_year, 2), 1, 1, 'R');
    $pdf->Cell(100, 8, 'Expected Year Expenses', 1);
    $pdf->Cell(90, 8, '$' . number_format($total_expected_expenses_year, 2), 1, 1, 'R');
    $pdf->Cell(100, 8, 'Expected Year Net Income', 1);
    $pdf->Cell(90, 8, '$' . number_format($total_expected_income_year - $total_expected_expenses_year, 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Year-to-Date Summary
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Year-to-Date (YTD) Budget Summary', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(100, 8, 'Actual YTD Income', 1);
    $pdf->Cell(90, 8, '$' . number_format($total_actual_income_ytd, 2), 1, 1, 'R');
    $pdf->Cell(100, 8, 'Actual YTD Expenses', 1);
    $pdf->Cell(90, 8, '$' . number_format($total_actual_expenses_ytd, 2), 1, 1, 'R');
    $pdf->Cell(100, 8, 'Actual YTD Net Income', 1);
    $pdf->Cell(90, 8, '$' . number_format($total_actual_income_ytd - $total_actual_expenses_ytd, 2), 1, 1, 'R');

    $pdf->Output('D', 'Budget_Report_' . $current_year . '.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Budget - MedNarrate</title>
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
    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="manage_finances.php">Manage Finances</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <h1>Generate Budget</h1>
            <a href="generate_budget.php?download=true" class="btn">Download Budget PDF</a>
        </div>
    </main>
</body>
</html>
