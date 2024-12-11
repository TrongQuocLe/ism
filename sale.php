<?php
require_once 'db_config.php';
require_once 'functions.php';
try {
    $conn = new mysqli($hn, $un, $pw, $db);
} catch (Exception $e) {
    mysql_fatal_error($e);
}
if (isset($_POST['buy'])) {
    customer_order($conn, $_POST['customer_id'], $_POST['product_id'], $_POST['quantity'], $_POST['price']);
}
$products = get_all_products($conn);
$customers = get_all_customers($conn);
$promotions_for_unsold_products_in_last_three_months = get_promotions_for_unsold_products_in_last_three_months($conn);
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
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_price']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h2 style="text-align: center;">Customers</h2>
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                    <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="summary">
        <h2>Buy</h2>
        <form method="POST" action="sale.php">
            <label for="customer_id">Customer ID:</label>
            <input type="text" id="customer_id" name="customer_id" required>
            <label for="product_id">Product ID:</label>
            <input type="text" id="product_id" name="product_id" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="0" required>
            <label for="price">Price:</label>
            <input type="number" id="price" step="0.01" name="price" required>
            <button type="submit" name="buy">Submit</button>
        </form>
    </div>

    <h2 style="text-align: center;">Promotions for Unsold Products in Last Three Months</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Original Price</th>
                <th>Discounted Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promotions_for_unsold_products_in_last_three_months as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product[0]); ?></td>
                    <td><?php echo htmlspecialchars($product[1]); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($product[2], 2)); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($product[3], 2)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>