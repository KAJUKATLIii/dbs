<?php
    session_start();

    include('connection.php');
    include('utils.php');
    include 'includes/auth_validate.php';

    try {
        if(isset($_GET['sell'])){
            $product_id = $_GET['product_id'];
            $product_quantity = $_GET['product_quantity'];
            
            // Check if product quantity is null or less than or equal to 0
            foreach($product_quantity as $quantity) {
                if($quantity === "" || $quantity <= 0) {
                    throw new Exception("Please enter a valid quantity for all selected products.");
                }
            }

            // Proceed with inserting order details
            $sql = "INSERT INTO orders VALUES (NULL,1,NULL,?,0,?,CURRENT_TIMESTAMP)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $_GET['id'],$_GET['payment_status2']);
            $stmt->execute();

            //Getting the last order id 
            $order_id = $con->query("SELECT * FROM orders ORDER BY order_id DESC LIMIT 1")->fetch_assoc()["order_id"];
            $total = 0;
            $subtotal = 0;
            $flag = 0;
            
            foreach($product_quantity as $quantity) {
                if($quantity != null){
                    $price_row = $con->query("SELECT product_price, product_stock FROM products WHERE product_id = ".$product_id[$flag])->fetch_assoc();
                    $product_price = $price_row['product_price'];
                    $product_stock = $price_row['product_stock'];
                    
                    // Check if ordered quantity is greater than available stock
                    if($quantity > $product_stock) {
                        throw new Exception("Insufficient stock for product ID: ".$product_id[$flag]);
                    }
                    
                    $subtotal = (int)$product_price * (int)$quantity;
                    $total += $subtotal;

                    $stmt = $con->prepare("INSERT INTO orders_product VALUES (NULL,?,?,?)");
                    $stmt->bind_param("sss", $order_id, $product_id[$flag], $quantity);
                    $stmt->execute();
                    
                    $con->query("UPDATE products SET product_stock = product_stock - $quantity WHERE product_id = $product_id[$flag]");
                    $flag++;
                }
            }
            
            $con->query("UPDATE orders SET total = $total WHERE order_id = $order_id");

            redirect("bill.php?order_id=".$order_id);
        }
    } catch(Exception $e) {
        alert_box($e->getMessage());
    }

    $sql = "SELECT * FROM customer WHERE customer_id = ".$_GET['id'];
    $result = $con->query($sql);
    
    $sql2 = "SELECT * FROM products";
    $result2 = $con->query($sql2);
    
    include_once('includes/header.php'); 
?>

<section id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>
    <div class="container">
        <h2>Sell Product</h2>
        <hr>
        <form class="form" action="sell.php">
            <?php while($row = $result->fetch_assoc()) { ?>
            <div class="form-group">
                <label class="control-label col-sm-2" for="email">Customer Name:</label>
                <?php echo $row['customer_name'] ?>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="control-label col-sm-2" for="pwd">Contact :</label>
                <?php echo $row['customer_phone'] ?>
            </div>
            <div class="form-group ">
                <br><label class="control-label col-sm-2" for="pwd">Payment Status :</label>
                <input class="form-check-input" type="radio" name="payment_status2" value="pending" id="flexRadioDefault2" checked> 
                Pending
                <input class="form-check-input" type="radio" name="payment_status2" value="paid" id="flexRadioDefault2">
                Paid
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2"> Choose Product :</label>
            <div class="container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th scope="col">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stocks</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row2 = $result2->fetch_assoc()) { ?>
                        <tr>
                            <td>
                                <div class="input-group-text">
                                    <input type="checkbox" name="product_id[]" value="<?php echo $row2['product_id'] ?>" aria-label="Checkbox for following text input">
                                </div>
                            </td>
                            <td><?php echo  $row2['product_name'] ."--". $row2['product_category'] ?></td>
                            <td><?php echo $row2['product_price'] ?></td>
                            <td><?php echo $row2['product_stock'] ?></td>
                            <td><input type="number" name="product_quantity[]" min="1" max="<?php echo $row2['product_stock'] ?>"></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="<?php echo $row['customer_id'] ?>">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default" name="sell" value="add_customer">Submit</button>
            </div>
        </div>
        </form>
        <?php } ?>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>