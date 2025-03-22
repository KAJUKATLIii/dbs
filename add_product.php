<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    try {
        // Prepare SQL query
        $sql = "INSERT INTO products (product_category, product_name, product_price, product_stock) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);

        // Bind parameters
        $stmt->bind_param(
            "ssdi", 
            $_POST['product_category'], 
            $_POST['product_name'], 
            $_POST['product_price'], 
            $_POST['product_stock']
        );

        // Execute query
        if ($stmt->execute()) {
            $_SESSION['success'] = "Product added successfully!";
            redirect("product.php"); // Redirect to product listing
        } else {
            throw new Exception("Failed to add product: " . $stmt->error);
        }
    } catch (Exception $err) {
        // Handle errors
        alert_box($err->getMessage());
    } finally {
        $stmt->close();
        $con->close();
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
        <h2>Add Product Details</h2>
        <hr>
        <form class="form" action="add_product.php" method="POST">
            <div class="form-group">
                <label class="control-label col-sm-2">Product Category:</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="text" class="form-control" placeholder="Product Category" name="product_category" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">Product Name:</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="text" class="form-control" placeholder="Product Name" name="product_name" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">Product Price:</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="number" step="0.01" class="form-control" placeholder="Product Price" name="product_price" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">Product Stock:</label>
                <div class="col-sm-10">
                    <input style="width:40%" type="number" class="form-control" placeholder="Product Stock" name="product_stock" required>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default" name="add_product" value="add_product">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
