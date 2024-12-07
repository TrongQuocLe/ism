<?php
/* reference to draw charts: https://www.youtube.com/watch?v=MJKUe-LnAlE 
and its share link in description: https://gist.github.com/mrmemmo/11572fe13b7b0e4d37772997f515c5f4 
reference to draw tables: https://devdevout.com/css/css-tables 
*/
require_once 'db_config.php';
require_once 'functions.php';
try {
    $conn = new mysqli($hn, $un, $pw, $db);
} catch (Exception $e) {
    mysql_fatal_error();
}
$sales_for_nov_2024 = get_sales_for_nov_2024($conn);
$gross_sales_for_nov_2024 = get_gross_sales_for_nov_2024($conn);
$is_increase_over_nov_2023 = is_increase_over_nov_2023($conn);
$is_increase_over_oct_2024 = is_increase_over_oct_2024($conn);
$best_selling_product = get_best_selling_product($conn);
$worst_selling_product = get_worst_selling_product($conn);
$most_profitable_vendor = get_most_profitable_vendor($conn);
$least_profitable_vendor = get_least_profitable_vendor($conn);
if (isset($_POST['submit'])) {
    update_product_quantity($conn, $_POST['product_id'], $_POST['quantity']);
}
$last_3_order = get_3_lastest_order($conn);
$promotions_for_unsold_products_in_last_three_months = get_promotions_for_unsold_products_in_last_three_months($conn);
$monthly_sales = get_total_gross_sales_monthly($conn);
$top_spending_customers = get_top_spending_customers($conn, 3);
$daily_sales = get_sales_each_day_in_last_30_days($conn);
$product_margins = get_profit_margin_percentage_by_product($conn);
$conn->close();
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dash board</title>
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
    <h1 style="text-align: center;">Dashboard</h1>
    <h2 style="text-align: center;">Sales Data in November 2024</h2>
    <table>
        <thead>
            <tr>
                <th>Sale Date</th>
                <th>Product Name</th>
                <th>Sale Gross ($)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales_for_nov_2024 as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($sale['sale_gross']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="summary">
        <p><span>Total Sale:</span> $<?php echo $gross_sales_for_nov_2024; ?></p>
        <p><?php echo $is_increase_over_nov_2023; ?></p>
        <p><?php echo $is_increase_over_oct_2024; ?></p>
    </div>
    <div class="summary">
        <h2>Additional Summary</h2>
        <p><span>Best Selling Product:</span> <?php echo htmlspecialchars($best_selling_product[0]); ?>
            (<?php echo htmlspecialchars($best_selling_product[1]); ?> units sold)</p>
        <p><span>Worst Selling Product:</span> <?php echo htmlspecialchars($worst_selling_product[0]); ?>
            (<?php echo htmlspecialchars($worst_selling_product[1]); ?> units sold)</p>
        <p><span>Most Profitable Vendor:</span> <?php echo htmlspecialchars($most_profitable_vendor[0]); ?>
            ($<?php echo htmlspecialchars(number_format($most_profitable_vendor[1], 2)); ?>)</p>
        <p><span>Least Profitable Vendor:</span> <?php echo htmlspecialchars($least_profitable_vendor[0]); ?>
            ($<?php echo htmlspecialchars(number_format($least_profitable_vendor[1], 2)); ?>)</p>
    </div>
    <div class="summary">
        <h2>Test Trigger</h2>
        <form method="POST" action="dashboard.php">
            <label for="product_id">Product ID:</label>
            <input type="text" id="product_id" name="product_id" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="0" required>
            <button type="submit" name="submit">Submit</button>
        </form>
    </div>

    <h2 style="text-align: center;">3 Lastest Order</h2>
    <table>
        <thead>
            <tr>
                <th>Order Id</th>
                <th>Product Id</th>
                <th>Product Name</th>
                <th>Order Date</th>
                <th>Order Quantity</th>
                <th>Order Status</th>
                <th>Vendor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($last_3_order as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order[0]); ?></td>
                    <td><?php echo htmlspecialchars($order[1]); ?></td>
                    <td><?php echo htmlspecialchars($order[2]); ?></td>
                    <td><?php echo htmlspecialchars($order[3]); ?></td>
                    <td><?php echo htmlspecialchars($order[4]); ?></td>
                    <td><?php echo htmlspecialchars($order[5]); ?></td>
                    <td><?php echo htmlspecialchars($order[6]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="summary">
        <h2>Monthly Sales (Line Chart)</h2>
        <canvas id="monthlySalesLineChart"></canvas>
    </div>
    <script>
        const salesData = <?php echo json_encode($monthly_sales); ?>;
        const labels = salesData.map(sale => sale.month); 
        const data = salesData.map(sale => parseFloat(sale.month_total_sales)); 
        const ctx = document.getElementById('monthlySalesLineChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Sales ($)',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `$${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Month' },
                        ticks: { autoSkip: false }
                    },
                    y: {
                        title: { display: true, text: 'Total Sales ($)' },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <div class="summary">
        <h2>Top Spending Customers (Column Graph)</h2>
        <canvas id="topSpendingCustomersGraph"></canvas>
    </div>
    <script>
        const topSpendingCustomersCtx = document.getElementById('topSpendingCustomersGraph').getContext('2d');
        new Chart(topSpendingCustomersCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($top_spending_customers, 'customer_name')); ?>,
                datasets: [{
                    label: 'Total Spent ($)',
                    data: <?php echo json_encode(array_column($top_spending_customers, 'total_spent')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Customer Name' } },
                    y: { title: { display: true, text: 'Total Spent ($)' } }
                }
            }
        });
    </script>

    <div class="summary">
        <h2>Sales Each Day in Last 30 Days (Column Graph)</h2>
        <canvas id="dailySalesGraph"></canvas>
    </div>
    <script>
        const dailySalesCtx = document.getElementById('dailySalesGraph').getContext('2d');
        new Chart(dailySalesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($daily_sales, 'sale_date')); ?>,
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: <?php echo json_encode(array_column($daily_sales, 'day_total_sales')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Sales ($)' } }
                }
            }
        });
    </script>

    <div class="summary">
        <h2>Product Profit Margins (Bar Chart)</h2>
        <canvas id="productProfitMarginsChart"></canvas>
    </div>

    <script>
        const productMargins = <?php echo json_encode($product_margins); ?>;
        const productLabels = productMargins.map(product => product.product_name);
        const profitMargins = productMargins.map(product => parseFloat(product.profit_margin_percentage));


        new Chart(document.getElementById('productProfitMarginsChart'), {
            type: 'bar',
            data: {
                labels: productLabels,
                datasets: [{
                    label: 'Profit Margin (%)',
                    data: profitMargins,
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Product Name' } },
                    y: { title: { display: true, text: 'Profit Margin (%)' }, beginAtZero: true }
                }
            }
        });
    </script>
</body>

</html>