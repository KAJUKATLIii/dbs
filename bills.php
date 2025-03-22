<?php
session_start();
include("connection.php");

$result = $con->query("SELECT
    orders.order_id,
    customer.customer_name,
    customer.customer_phone,
    products.product_name,
    products.product_category,
    orders_product.quantity,
    products.product_price,
    orders.date,
    orders.payment_status
FROM
    `orders_product`,
    customer,
    products,
    orders
WHERE
    orders_product.order_id = orders.order_id AND 
    products.product_id = orders_product.product_id AND
     orders.customer_id = customer.customer_id AND
      orders_product.order_id = ".$_GET["order_id"]);

$rows = $result->fetch_assoc();
$customer_Name = $rows['customer_name'];
$customer_phone = $rows['customer_phone'];
$payment_status = $rows['payment_status'];
$order_date = $rows['date'];
mysqli_data_seek($result, 0);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        .invoice {
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            width: 100%;
        }

        .invoice header,
        .invoice footer {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f7f7f7;
        }

        footer {
            font-size: 0.8rem;
            color: #777;
        }

        @media print {
            .hidden-print {
                display: none;
            }

            .invoice {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="toolbar hidden-print">
            <div class="text-end">
                <button onclick="window.print()" class="btn btn-dark"><i class="fa fa-print"></i> Print</button>
                <button id="export-pdf" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>
            </div>
            <hr>
        </div>
        <div class="invoice" id="invoice">
            <header>
                <div class="row">
                    <div class="col">
                        <img src="assets/images/logo-icon.png" width="80" alt="">
                    </div>
                    <div class="col text-end">
                        <h2>SHARON DISTRIBUTION ENTERPRISES</h2>
                        <div>RUNGTA, RAIPUR</div>
                        <div>8080939426</div>
                        <div>sharobdistribution@gmail.com</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row">
                    <div class="col">
                        <h4>Invoice To:</h4>
                        <p>
                            <?php echo $customer_Name; ?><br>
                            <a href="tel:+91<?php echo $customer_phone; ?>">+91 <?php echo $customer_phone; ?></a>
                        </p>
                    </div>
                    <div class="col text-end">
                        <h4>Invoice Details:</h4>
                        <p>
                            Order No: <?php echo $_GET["order_id"]; ?><br>
                            Date: <?php echo $order_date; ?><br>
                            Payment Status: <?php echo $payment_status; ?>
                        </p>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price (₹)</th>
                            <th>Quantity</th>
                            <th>Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; $Sr = 1;
                        while ($row = $result->fetch_assoc()) {
                            $total_per_item = $row['product_price'] * $row['quantity'];
                            $total += $total_per_item;
                        ?>
                        <tr>
                            <td><?php echo $Sr; ?></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['product_category']; ?></td>
                            <td><?php echo $row['product_price']; ?> ₹</td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $total_per_item; ?> ₹</td>
                        </tr>
                        <?php $Sr++; } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td>Subtotal</td>
                            <td><?php echo $total; ?> ₹</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>Tax (5%)</td>
                            <td><?php echo $total * 0.05; ?> ₹</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>Grand Total</td>
                            <td><?php echo $total + ($total * 0.05); ?> ₹</td>
                        </tr>
                    </tfoot>
                </table>
                <p class="thanks mt-4">Thank you!</p>
            </main>
            <footer>
                Invoice was created on a computer and is valid without a signature and seal.
            </footer>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        document.getElementById("export-pdf").addEventListener("click", () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF("p", "pt", "a4");
            doc.html(document.querySelector("#invoice"), {
                callback: function (doc) {
                    doc.save("invoice.pdf");
                },
                margin: [10, 10, 10, 10],
                autoPaging: "text",
            });
        });
    </script>
</body>

</html>
