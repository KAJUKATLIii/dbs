<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

// Check if the form is being submitted
if (isset($_GET['edit_vendor'])) {
    // Validate the phone number to be exactly 10 digits
    if (strlen($_GET['vendor_phone']) != 10 || !is_numeric($_GET['vendor_phone'])) {
        alert_box("Phone number must be exactly 10 digits.");
        return; // Prevent further execution if the phone number is invalid
    }

    // Check if the phone number already exists in the database (excluding the current vendor's phone)
    $phone = $_GET['vendor_phone'];
    $checkPhoneQuery = "SELECT * FROM vendor WHERE vendor_phone = ? AND id != ?";
    $stmtCheck = $con->prepare($checkPhoneQuery);
    $stmtCheck->bind_param("si", $phone, $_GET['id']);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        alert_box("Phone number already exists. Please enter a different number.");
        return; // Stop form submission if phone number exists
    }

    try {
        // Update vendor details in the database
        $sql = "UPDATE vendor SET vendor_name = ?, vendor_phone = ?, product_id = ?, 
                                  vendor_quantity = ?, vendor_price = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssi", $_GET['vendor_name'], $_GET['vendor_phone'], $_GET['product_id'],
                                   $_GET['vendor_quantity'], $_GET['vendor_price'], $_GET['id']);
        $stmt->execute();

        $_SESSION['success'] = "Edited Successfully";
        redirect("vendors.php"); // Redirect to vendors list page after successful update

    } catch (mysqli_sql_exception $err) {
        alert_box("An error occurred: " . $err->getMessage());
    }
}

// Fetch current vendor details for editing
$sql = "SELECT * FROM vendor WHERE id = " . $_GET['id'];
$result = $con->query($sql);

include_once('includes/header.php');
?>

<script>
    // JavaScript function for client-side phone number validation (10 digits only)
    function validatePhoneNumber() {
        var phone = document.getElementsByName('vendor_phone')[0].value;
        var phoneRegex = /^[0-9]{10}$/; // Check for exactly 10 digits

        if (!phone.match(phoneRegex)) {
            alert("Please enter a valid 10-digit phone number.");
            return false; // Prevent form submission if the phone number is invalid
        }
        return true;
    }
</script>

<section id="page-wrapper">
    <div class="container">
        <h2>Edit Vendor Details </h2>
        <hr>
        <form class="form" action="edit_vendor.php" method="GET" onsubmit="return validatePhoneNumber()">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="form-group">
                <label class="control-label col-sm-2" for="email">Vendor Name:</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="text" value="<?php echo $row['vendor_name'] ?>" class="form-control"
                        placeholder="Vendor Name" name="vendor_name">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Contact :</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="number" value="<?php echo $row['vendor_phone'] ?>"
                        class="form-control" placeholder="Contact" name="vendor_phone">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="email">Product Category</label>
                <div class="col-sm-10">
                    <select style="width:40%" class="form-control" name="product_id">
                        <?php
                        // Fetch product categories to populate the dropdown
                        $sql1 = "SELECT * FROM products";
                        $result1 = $con->query($sql1);
                        while ($row1 = $result1->fetch_assoc()) {
                        ?>
                        <option value="<?php echo $row1['product_id'] ?>" <?php if ($row1['product_id'] == $row['product_id']) echo 'selected'; ?>>
                            <?php echo $row1["product_category"] . "--" . $row1['product_name']; ?> 
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Quantity :</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="number" value="<?php echo $row['vendor_quantity'] ?>"
                        class="form-control" placeholder="Quantity" name="vendor_quantity">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Price:</label>
                <div class="col-sm-10">
                    <input style="width:30%" type="number" value="<?php echo $row['vendor_price'] ?>"
                        class="form-control" placeholder="Price " name="vendor_price">
                </div>
            </div>

            <div class="form-group">
                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default" name="edit_vendor" value="edit_vendor">Submit</button>
                </div>
            </div>
            <?php } ?>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
