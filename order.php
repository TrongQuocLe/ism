<?php
require_once 'db_config.php';
require_once 'functions.php';
try {
    $conn = new mysqli($hn, $un, $pw, $db);
} catch (Exception $e) {
    mysql_fatal_error($e);
}
if (isset($_POST['order'])) {
    vendor_order($conn, $_POST['vendor_id'], $_POST['product_id'], $_POST['quantity']);
}
$products = get_all_products($conn);
$orders = get_all_orders($conn);
$conn->close();
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
            text-align: left;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .summary {
            width: 80%;
            margin: 20px auto;
            font-size: 18px;
            line-height: 1.8;
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .summary span {
            font-weight: bold;
        }

        canvas {
            max-width: 100%;
            height: 400px;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">Welcome</h1>
    <h2 style="text-align: center;">Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Cost</th>
                <th>Min Quantity</th>
                <th>Vendor Id</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_cost']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_min_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['vendor_id']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table><h2 style="text-align: center;">Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product ID</th>
                <th>Vendor ID</th>
                <th>Quantity</th>
                <th>Date</th>
                <th>Order Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['vendor_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="summary">
        <h2>New Order</h2>
        <form method="POST" action="order.php">
            <label for="vendor_id">Vendor ID</label>
            <input type="number" name="vendor_id" id="vendor_id" required>
            <label for="product_id">Product ID</label>
            <input type="number" name="product_id" id="product_id" required>
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" required>
            <button type="submit" name="order">Order</button>
        </form>
    </div>

</body>

</html>