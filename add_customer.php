<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

$popup_message = ''; // Variable to store the popup message

if (isset($_GET['add_customer'])) {
    $customer_name = trim($_GET['customer_name']);
    $customer_phone = trim($_GET['customer_phone']);

    // Server-side validation for 10-digit phone number
    if (!preg_match('/^\d{10}$/', $customer_phone)) {
        $popup_message = "Invalid phone number. Please enter a valid 10-digit phone number.";
    } else {
        try {
            // Check if the phone number already exists
            $check_sql = "SELECT * FROM customer WHERE customer_phone = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param("s", $customer_phone);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $popup_message = "Phone number already exists. Please use unique details.";
            } else {
                // Insert the new customer
                $sql = "INSERT INTO customer (customer_name, customer_phone) VALUES (?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ss", $customer_name, $customer_phone);
                $stmt->execute();

                $_SESSION['success'] = "Customer added successfully.";
                redirect('customer.php');
                exit;
            }
        } catch (mysqli_sql_exception $err) {
            $popup_message = "Error: " . mysqli_error($con);
        } finally {
            if (isset($stmt)) $stmt->close();
            if (isset($check_stmt)) $check_stmt->close();
            $con->close();
        }
    }
}

include_once('includes/header.php');
?>

<style>
.form-group {
    padding-bottom: 2%;
}
</style>

<section id="page-wrapper">
    <div class="container">
        <h2>Add Customer Details</h2>
        <hr>
        <form class="form" action="add_customer.php" method="GET" onsubmit="return validateForm()">
            <div class="form-group">
                <label class="control-label col-sm-2" for="customer_name">Customer Name:</label>
                <div class="col-sm-10">
                    <input
                        style="width:40%"
                        type="text"
                        class="form-control"
                        placeholder="Customer Name"
                        name="customer_name"
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
                        pattern="\d{10}"
                        maxlength="10"
                        required
                        title="Please enter a valid 10-digit phone number"
                    >
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button
                        type="submit"
                        class="btn btn-default"
                        name="add_customer"
                        value="add_customer"
                    >
                        Submit
                    </button>
                </div>
            </div>
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
