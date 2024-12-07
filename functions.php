<?php // functions.php
function mysql_fatal_error($e)
{
    // Comment out below lineafter debugging
    echo $e;
    echo <<<_END
    We are sorry, but it was not possible to complete
    the requested task.
    Please try again. If you are still having problems,
    please <a href="mailto:admin@server.com">email
    our administrator</a>. Thank you.
    _END;
    die();
}
// 1. 
// Sales for November 2024
function get_sales_for_nov_2024($conn)
{
    $query =
        "SELECT s.sale_date, p.product_name, s.sale_gross  
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        WHERE MONTH(sale_date) = 11 AND YEAR(sale_date) = 2024;
        ";
    try {
        $result = $conn->query($query);
        $sales_for_nov_2024 = $result->fetch_all(MYSQLI_ASSOC);
        // $sales_for_nov_2024 = $result->fetch_all();
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $sales_for_nov_2024;
}

// Gross sale for November 2024
function get_gross_sales_for_nov_2024($conn)
{
    $query = "SELECT SUM(sale_gross) FROM sales WHERE MONTH(sale_date) = 11 AND YEAR(sale_date) = 2024";
    try {
        $result = $conn->query($query);
        $gross_sales_for_nov_2024 = ($result->fetch_all())[0][0];
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $gross_sales_for_nov_2024;
}
// Is this an increase over the previous year - November 2023
function is_increase_over_nov_2023($conn)
{
    $query =
        "SELECT 
        IF(
            SUM(CASE WHEN YEAR(sale_date) = 2024 AND MONTH(sale_date) = 11 THEN sale_gross ELSE 0 END) 
            > 
            SUM(CASE WHEN YEAR(sale_date) = 2023 AND MONTH(sale_date) = 11 THEN sale_gross ELSE 0 END)
            , 'Yes, this is an increase over the previous year November 2023', 'No, this is not an increase over the previous year November 2023') 
        AS sales_increase
        FROM sales;";
    // query below take more time than the one above
    // $query =
    //     "SELECT 
    //     IF((SELECT SUM(sale_gross) FROM sales WHERE MONTH(sale_date) = 11 AND YEAR(sale_date) = 2024)  >
    //        (SELECT SUM(sale_gross) FROM sales WHERE MONTH(sale_date) = 11 AND YEAR(sale_date) = 2023) , 'Yes', 'No') 
    //        AS sales_increase;";
    try {
        $result = $conn->query($query);
        $is_sales_increase = ($result->fetch_all())[0][0];
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $is_sales_increase;
}

// is  this  a increase over the previous month - October 2024
function is_increase_over_oct_2024($conn)
{
    $query =
        "SELECT 
        IF(
            SUM(CASE WHEN YEAR(sale_date) = 2024 AND MONTH(sale_date) = 10 THEN sale_gross ELSE 0 END) 
            > 
            SUM(CASE WHEN YEAR(sale_date) = 2024 AND MONTH(sale_date) = 9 THEN sale_gross ELSE 0 END)
            , 'Yes, this is an increase over the previous month October 2024', 'No, this is not an increase over the previous month October 2024') 
        AS sales_increase
        FROM sales;";
    try {
        $result = $conn->query($query);
        $is_sales_increase = ($result->fetch_all())[0][0];
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $is_sales_increase;
}

// 2. 
// Identify the Best Seller
function get_best_selling_product($conn)
{
    $query =
        "SELECT 
            p.product_name AS product_name, 
            SUM(s.sale_quantity) AS total_quantity_sold
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        GROUP BY s.product_id
        ORDER BY total_quantity_sold DESC
        LIMIT 1;
        ";
    try {
        $result = $conn->query($query);
        $best_selling_product = $result->fetch_all()[0];
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $best_selling_product;
}

// Identify the Worst Seller
function get_worst_selling_product($conn)
{
    $query =
        "SELECT 
        p.product_name AS product_name, 
        SUM(s.sale_quantity) AS total_quantity_sold
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        GROUP BY s.product_id
        ORDER BY total_quantity_sold ASC
        LIMIT 1;
        ";
    try {
        $result = $conn->query($query);
        $worst_selling_product = $result->fetch_all()[0];
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $worst_selling_product;
}

// Identify the Most Profitable Vendor
function get_most_profitable_vendor($conn)
{
    $query =
        "SELECT 
            v.vendor_name, 
            SUM(s.sale_gross) - (SUM(p.product_cost * s.sale_quantity)) AS total_profit
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        JOIN vendors v ON p.vendor_id = v.vendor_id
        GROUP BY v.vendor_id
        ORDER BY total_profit DESC
        LIMIT 1;
        ";
    
    try {
        $result = $conn->query($query);
        $most_profitable_vendor = $result->fetch_all()[0];
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $most_profitable_vendor;
}

// Identify the Least Profitable Vendor
function get_least_profitable_vendor($conn)
{
    $query =
        "SELECT 
            v.vendor_name AS `Least Profitable Vendor`,
            SUM(s.sale_gross) - (SUM(p.product_cost * s.sale_quantity)) AS `Total Profit`
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        JOIN vendors v ON p.vendor_id = v.vendor_id
        GROUP BY v.vendor_id
        ORDER BY `Total Profit` ASC
        LIMIT 1;
        ";
    try {
        // $result = $conn->query($query);
        // $least_profitable_vendor = $result->fetch_all()[0];
        // $result->close();

        $least_profitable_vendor = $conn->query($query)->fetch_all()[0];

        // $least_profitable_vendor = $conn->query($query)->fetch_assoc();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $least_profitable_vendor;
}

// 4. Trigger when the stock is less than the threshold

// Set Product Quantity to show how the trigger (check stock < threshold) works
function update_product_quantity($conn, $product_id, $quantity)
{
    $quantity = (int)$quantity;
    $product_id = (int)$product_id;
    $query = "UPDATE products SET product_quantity = ? WHERE product_id = ?;";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $quantity, $product_id);
        $stmt->execute();
    } catch (Exception $e) {
        mysql_fatal_error($e);
    }
    $stmt->close();
}

// 5. Identify items that haven’t sold in the past 3 months and come up with a sale. 
// This sale would list such items and sell them at a discount. 

// delete last three month sales to show how the trigger works
function delete_last_three_month_sales($conn)
{
    $query = "DELETE FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH);";
    try {
        $conn->query($query);
    } catch (Exception $e) {
        mysql_fatal_error();
    }
}
// Identify items that haven’t sold in the past 3 months and come up with a sale
function get_promotions_for_unsold_products_in_last_three_months($conn)
{
    $query =
        "SELECT 
            p.product_id, 
            p.product_name, 
            p.product_price AS original_price,
            (p.product_price * 0.8) AS discounted_price
        FROM products p
        WHERE p.product_id NOT IN (
            SELECT DISTINCT s.product_id 
            FROM sales s
            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH));
        ";
    try {
        $result = $conn->query($query);
        $promotions = $result->fetch_all();
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $promotions;
}




// 6. Create a report that lists total sales per month for current year
function get_total_gross_sales_monthly($conn)
{
    $query =
        "SELECT 
        DATE_FORMAT(sale_date, '%M %Y') AS month,
        SUM(sale_gross) AS month_total_sales
        FROM sales
        GROUP BY month
        ORDER BY month;
        ";
    // $query =
    //     "SELECT 
    //     MONTH(sale_date) AS month, 
    //     SUM(sale_gross) AS month_total_sales
    //     FROM sales
    //     GROUP BY month
    //     ORDER BY month;
    //     ";
    try {
        $result = $conn->query($query);
        $sales_each_month = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $sales_each_month;
}

// 7. Create a report that lists top spending customers
function get_top_spending_customers($conn, $limit)
{
    $limit = filter_var(value: $limit, filter: FILTER_VALIDATE_INT);
    $query =
        "SELECT 
            c.customer_name, 
            COUNT(s.sale_id) AS purchase_count, 
            SUM(s.sale_gross) AS total_spent
        FROM customers c
        JOIN sales s ON c.customer_id = s.customer_id
        GROUP BY c.customer_id
        ORDER BY total_spent DESC
        LIMIT ?;
        ";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $top_spending_customers = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
        $stmt->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $top_spending_customers;
}

// 8. Create a report that lists total sales per day for last 30 days
function get_sales_each_day_in_last_30_days($conn)
{
    $query =
        "SELECT 
        sale_date, 
        SUM(sale_gross) AS day_total_sales
        FROM sales
        WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY sale_date
        ORDER BY sale_date;
        ";
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $sales_each_day = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
        $stmt->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $sales_each_day;
}
// 9. Get latest order to show how the trigger (check stock < threshold) works
function get_3_lastest_order($conn)
{
    $query =
        "SELECT o.order_id, p.product_id, p.product_name, o.order_date, o.order_quantity, o.order_status, v.vendor_name AS vendor_name
        FROM orders o
        JOIN vendors v ON o.vendor_id = v.vendor_id
        JOIN products p ON o.product_id = p.product_id
        ORDER BY o.order_id DESC
        LIMIT 3;";
    try {
        // $lastest_order = $conn->query($query)->fetch_assoc();

        $lastest_order = $conn->query($query)->fetch_all();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $lastest_order;
}
// 10. Profit Margin by Product
function get_profit_margin_percentage_by_product($conn)
{
    $query =
        "SELECT 
            p.product_id, 
            p.product_name, 
            FORMAT ((SUM(s.sale_gross) / SUM(p.product_cost * s.sale_quantity) ) * 100, 2) AS profit_margin_percentage
        FROM products p
        JOIN sales s ON p.product_id = s.product_id
        GROUP BY p.product_id;
        ";
    try {
        $result = $conn->query($query);
        $profit_margin_by_product = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $profit_margin_by_product;
}


// Customer
function get_all_products($conn)
{
    $query = 
        "SELECT 
            product_id,
            product_name,
            product_description,
            product_quantity,
            product_cost,
            product_price,
            vendor_id
        FROM products";
    try {
        $result = $conn->query($query);
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $products;
}
function get_all_customers($conn)
{
    $query = 
        "SELECT 
            customer_id,
            customer_name
        FROM customers";
    try {
        $result = $conn->query($query);
        $customers = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $customers;
}
function customer_order($conn, $customer_id, $product_id, $quantity, $price)
{
    $quantity = (int)$quantity;
    $price = (float)$price;
    $sale_gross = $price * $quantity;
    $query = 
        "INSERT INTO sales (customer_id, product_id, sale_quantity, sale_date, sale_gross)
        VALUES (?, ?, ?, CURDATE(), ?)";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiid', $customer_id, $product_id, $quantity, $sale_gross);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
}

// Vendor
function get_all_orders($conn)
{
    $query = 
        "SELECT 
            order_id,
            product_id,
            vendor_id,
            order_quantity,
            order_status,
            order_date
        FROM orders";
    try {
        $result = $conn->query($query);
        $vendors = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
    return $vendors;
}
function vendor_order($conn, $vendor_id, $product_id, $quantity)
{
    $query = 
        "INSERT INTO orders (vendor_id, product_id, order_quantity, order_date, order_status)
        VALUES (?, ?, ?, CURDATE(), 'Pending')";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iis', $vendor_id, $product_id, $quantity);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
}
function complete_order($conn, $order_id)
{
    $order_id = (int)$order_id;
    $query = 
        "UPDATE orders
        SET order_status = 'Completed'
        WHERE order_id = ?";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        mysql_fatal_error();
    }
}
?>