<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching filter options
$filterMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$filterYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch filtered data for "Paid" orders only
$sql = "SELECT o.order_id, o.total, o.payment_status, o.date, 
        c.customer_name, c.customer_phone
        FROM orders o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        WHERE MONTH(o.date) = ? AND YEAR(o.date) = ? AND o.type = 1 AND o.payment_status = 'Paid'"; // Only "Sold" and "Paid" orders

if (!empty($searchKeyword)) {
    $sql .= " AND c.customer_name LIKE ?";
}

$stmt = $conn->prepare($sql);

// Bind parameters based on whether searchKeyword is set
if (!empty($searchKeyword)) {
    $searchKeyword = "%$searchKeyword%";
    $stmt->bind_param("iis", $filterMonth, $filterYear, $searchKeyword);
} else {
    $stmt->bind_param("ii", $filterMonth, $filterYear);
}

$stmt->execute();
$result = $stmt->get_result();

// Calculate total revenue for the given filter period, considering only "Sold" and "Paid" orders
$totalRevenueQuery = "SELECT SUM(total) AS total_revenue FROM orders WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 1 AND payment_status = 'Paid'";
$stmtRevenue = $conn->prepare($totalRevenueQuery);
$stmtRevenue->bind_param("ii", $filterMonth, $filterYear);
$stmtRevenue->execute();
$revenueResult = $stmtRevenue->get_result();
$totalRevenue = $revenueResult->fetch_assoc()['total_revenue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        .container { margin-top: 20px; }
        .form-inline { display: flex; gap: 10px; }
        table { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Monthly Reports</h2>

        <!-- Filter Form -->
        <form method="GET" class="form-inline">
            <div class="form-group">
                <label for="month">Month:</label>
                <select name="month" id="month" class="form-select">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $filterMonth) ? 'selected' : '';
                        echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="year">Year:</label>
                <select name="year" id="year" class="form-select">
                    <?php
                    for ($i = date('Y') - 5; $i <= date('Y'); $i++) {
                        $selected = ($i == $filterYear) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Customer">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Total Revenue -->
        <div class="mt-4">
            <h4>Total Revenue for <?php echo date('F', mktime(0, 0, 0, $filterMonth, 1)) . ' ' . $filterYear; ?>: 
                <strong><?php echo number_format($totalRevenue, 2); ?> </strong></h4>
        </div>

        <!-- Report Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total</th>
                    <th>Payment Status</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Customer Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['order_id']}</td>
                                <td>{$row['total']}</td>
                                <td>{$row['payment_status']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['customer_name']}</td>
                                <td>{$row['customer_phone']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
