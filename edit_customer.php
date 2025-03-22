<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

$popup_message = ''; // To store validation messages for popups

if (isset($_GET['edit_customer'])) {
    try {
        // Check if the phone number already exists
        $sql_check = "SELECT customer_id FROM customer WHERE customer_phone = ? AND customer_id != ?";
        $stmt_check = $con->prepare($sql_check);
        $stmt_check->bind_param("si", $_GET['customer_phone'], $_GET['id']);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Phone number already exists
            $popup_message = "Phone number already exists. Please use a unique phone number.";
        } else {
            // Update the customer in the database
            $sql = "UPDATE customer SET customer_name = ?, customer_phone = ? WHERE customer_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssi", $_GET['customer_name'], $_GET['customer_phone'], $_GET['id']);
            $stmt->execute();

            $_SESSION['success'] = "Customer details updated successfully.";
            redirect("customer.php");
            exit;
        }
    } catch (mysqli_sql_exception $err) {
        $popup_message = "Error: " . $err->getMessage();
    }
}

// Fetch the customer details
$sql = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

include_once('includes/header.php');
?>

<section id="page-wrapper">
    <div class="container">
        <h2>Edit Customer Details</h2>
        <hr>
        <form class="form" action="edit_customer.php" method="GET">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="customer_name">Customer Name:</label>
                    <div class="col-sm-10">
                        <input
                            style="width:40%"
                            type="text"
                            class="form-control"
                            placeholder="Customer Name"
                            name="customer_name"
                            value="<?php echo htmlspecialchars($row['customer_name']); ?>"
                            required
                        >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="customer_phone">Contact:</label>
                    <div class="col-sm-10">
                        <input
                            style="width:40%"
                            type="text"
                            class="form-control"
                            placeholder="Contact"
                            name="customer_phone"
                            value="<?php echo htmlspecialchars($row['customer_phone']); ?>"
                            maxlength="10"
                            required
                            title="Please enter a valid 10-digit phone number"
                        >
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['customer_id']); ?>">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button
                            type="submit"
                            class="btn btn-default"
                            name="edit_customer"
                            value="edit_customer"
                        >
                            Submit
                        </button>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>
</section>

<script>
    // Display popup if there's a server-side error message
    const popupMessage = "<?php echo addslashes($popup_message); ?>";
    if (popupMessage) {
        alert(popupMessage);
    }
</script>

<?php include 'includes/footer.php'; ?>
