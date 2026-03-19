<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html");
    exit();
}

// Determine if the user is viewing the yearly or monthly budget
$view = $_GET['view'] ?? 'monthly';  // Default to monthly view
$current_year = date('Y');
$current_month = date('m');

// Fetch actual and expected incomes and expenses based on the view
if ($view === 'yearly') {
    $incomes = $conn->query("SELECT * FROM incomes WHERE YEAR(date) = $current_year")->fetch_all(MYSQLI_ASSOC);
    $expenses = $conn->query("SELECT * FROM expenses WHERE YEAR(date) = $current_year")->fetch_all(MYSQLI_ASSOC);
    $expected_incomes = $conn->query("SELECT * FROM expected_incomes WHERE YEAR(date) = $current_year")->fetch_all(MYSQLI_ASSOC);
    $expected_expenses = $conn->query("SELECT * FROM expected_expenses WHERE YEAR(date) = $current_year")->fetch_all(MYSQLI_ASSOC);
} else {
    $incomes = $conn->query("SELECT * FROM incomes WHERE YEAR(date) = $current_year AND MONTH(date) = $current_month")->fetch_all(MYSQLI_ASSOC);
    $expenses = $conn->query("SELECT * FROM expenses WHERE YEAR(date) = $current_year AND MONTH(date) = $current_month")->fetch_all(MYSQLI_ASSOC);
    $expected_incomes = $conn->query("SELECT * FROM expected_incomes WHERE YEAR(date) = $current_year AND MONTH(date) = $current_month")->fetch_all(MYSQLI_ASSOC);
    $expected_expenses = $conn->query("SELECT * FROM expected_expenses WHERE YEAR(date) = $current_year AND MONTH(date) = $current_month")->fetch_all(MYSQLI_ASSOC);
}

// Fetch pending invoices to include as expected income
$sql_pending_invoices = "SELECT * FROM invoices WHERE status = 'pending'";
$pending_invoices = $conn->query($sql_pending_invoices)->fetch_all(MYSQLI_ASSOC);
$total_pending_income = 0;
foreach ($pending_invoices as $invoice) {
    $total_pending_income += $invoice['amount'];
    $expected_incomes[] = [
        'expected_income_id' => $invoice['invoice_id'],  // Use invoice ID
        'description' => 'Pending Invoice: ' . htmlspecialchars($invoice['client_name']),
        'date' => $invoice['date_created'],
        'amount' => $invoice['amount']
    ];
}

// Calculate totals
$total_actual_income = array_sum(array_column($incomes, 'amount'));
$total_expected_income = array_sum(array_column($expected_incomes, 'amount'));
$total_actual_expense = array_sum(array_column($expenses, 'amount'));
$total_expected_expense = array_sum(array_column($expected_expenses, 'amount'));

// Calculate profit/loss
$actual_profit_loss = $total_actual_income - $total_actual_expense;
$expected_profit_loss = $total_expected_income - $total_expected_expense;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Budget - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"> <!-- Favicon -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            width: 100%;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
            text-align: center;
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

        main {
            flex: 1;
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .content {
            width: 100%;
            max-width: 1200px; /* Adjusted for better alignment */
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
            text-align: center;
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
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .content-centered {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .summary-table {
            width: 100%;
            margin-top: 20px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f4f4f4;
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
        <div class="content content-centered">
            <h1>Interactive Budget</h1>

            <div class="view-switch">
                <a href="interactive_budget.php?view=monthly" class="btn">Monthly View</a>
                <a href="interactive_budget.php?view=yearly" class="btn">Yearly View</a>
            </div>

            <div class="actions">
                <a href="add_expected_income.php" class="btn">Add Expected Income</a>
                <a href="add_expected_expense.php" class="btn">Add Expected Expense</a>
            </div>

            <h2><?php echo ucfirst($view); ?> Income</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($incomes)): ?>
                        <tr>
                            <td colspan="4">No <?php echo $view; ?> incomes found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($incomes as $income): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($income['description']); ?></td>
                                <td><?php echo htmlspecialchars($income['date']); ?></td>
                                <td>$<?php echo htmlspecialchars($income['amount']); ?></td>
                                <td>
                                    <a href="edit_income.php?id=<?php echo htmlspecialchars($income['income_id']); ?>" class="btn">Edit</a>
                                    <a href="delete_income.php?id=<?php echo htmlspecialchars($income['income_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this income?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="2">Total Actual Income</td>
                            <td>$<?php echo number_format($total_actual_income, 2); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h2><?php echo ucfirst($view); ?> Expected Income (Including Pending Invoices)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expected_incomes)): ?>
                        <tr>
                            <td colspan="4">No expected incomes found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expected_incomes as $income): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($income['description']); ?></td>
                                <td><?php echo htmlspecialchars($income['date']); ?></td>
                                <td>$<?php echo htmlspecialchars($income['amount']); ?></td>
                                <td>
                                    <a href="edit_expected_income.php?id=<?php echo htmlspecialchars($income['expected_income_id']); ?>" class="btn">Edit</a>
                                    <a href="delete_expected_income.php?id=<?php echo htmlspecialchars($income['expected_income_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this expected income?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="2">Total Expected Income</td>
                            <td>$<?php echo number_format($total_expected_income, 2); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h2><?php echo ucfirst($view); ?> Expenses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="4">No <?php echo $view; ?> expenses found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td><?php echo htmlspecialchars($expense['date']); ?></td>
                                <td>$<?php echo htmlspecialchars($expense['amount']); ?></td>
                                <td>
                                    <a href="edit_expense.php?id=<?php echo htmlspecialchars($expense['expense_id']); ?>" class="btn">Edit</a>
                                    <a href="delete_expense.php?id=<?php echo htmlspecialchars($expense['expense_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="2">Total Actual Expenses</td>
                            <td>$<?php echo number_format($total_actual_expense, 2); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h2><?php echo ucfirst($view); ?> Expected Expenses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expected_expenses)): ?>
                        <tr>
                            <td colspan="4">No expected expenses found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expected_expenses as $expense): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td><?php echo htmlspecialchars($expense['date']); ?></td>
                                <td>$<?php echo htmlspecialchars($expense['amount']); ?></td>
                                <td>
                                    <a href="edit_expected_expense.php?id=<?php echo htmlspecialchars($expense['expected_expense_id']); ?>" class="btn">Edit</a>
                                    <a href="delete_expected_expense.php?id=<?php echo htmlspecialchars($expense['expected_expense_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this expected expense?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="2">Total Expected Expenses</td>
                            <td>$<?php echo number_format($total_expected_expense, 2); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="summary">
                <h2>Summary</h2>

                <table class="summary-table">
                    <thead>
                        <tr>
                            <th colspan="2">Expected Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Expected Income</td>
                            <td>$<?php echo number_format($total_expected_income, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Expected Expenses</td>
                            <td>$<?php echo number_format($total_expected_expense, 2); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td>Expected Profit/Loss</td>
                            <td>$<?php echo number_format($expected_profit_loss, 2); ?></td>
                        </tr>
                    </tbody>
                </table>

                <table class="summary-table">
                    <thead>
                        <tr>
                            <th colspan="2">Actual Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Actual Income</td>
                            <td>$<?php echo number_format($total_actual_income, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Actual Expenses</td>
                            <td>$<?php echo number_format($total_actual_expense, 2); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td>Actual Profit/Loss</td>
                            <td>$<?php echo number_format($actual_profit_loss, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
