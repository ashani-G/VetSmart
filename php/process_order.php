<?php
session_start();
require_once 'config.php'; // Includes Stripe keys and autoloader
require_once 'db_connect.php'; // Includes database connection

// --- Security Checks ---
if (!isset($_GET['payment_intent']) || !isset($_GET['payment_intent_client_secret']) || !isset($_GET['address'])) {
    die("Invalid access.");
}
if (empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
    die("Session expired or cart is empty.");
}

try {
    // Retrieve the PaymentIntent from Stripe to verify its status
    $paymentIntent = \Stripe\PaymentIntent::retrieve($_GET['payment_intent']);

    // Check if the payment was successful
    if ($paymentIntent->status === 'succeeded') {
        
        // --- Payment is successful, now save the order to the database ---
        $client_id = $_SESSION['user_id'];
        $shipping_address = urldecode($_GET['address']);
        $payment_intent_id = $paymentIntent->id;
        
        // Calculate total amount again for security
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Use a database transaction to ensure all queries succeed or none do.
        mysqli_begin_transaction($conn);

        try {
            // 1. Insert into `orders` table
            $sql_order = "INSERT INTO orders (client_id, total_amount, shipping_address, stripe_payment_intent_id, order_status) VALUES (?, ?, ?, ?, 'Processing')";
            $stmt_order = mysqli_prepare($conn, $sql_order);
            mysqli_stmt_bind_param($stmt_order, "idss", $client_id, $total_amount, $shipping_address, $payment_intent_id);
            mysqli_stmt_execute($stmt_order);
            $order_id = mysqli_insert_id($conn); // Get the ID of the new order

            // 2. Insert each item from the cart into `order_items` and update stock
            $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($conn, $sql_item);

            $sql_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $stmt_stock = mysqli_prepare($conn, $sql_stock);

            foreach ($_SESSION['cart'] as $product_id => $item) {
                mysqli_stmt_bind_param($stmt_item, "iiid", $order_id, $product_id, $item['quantity'], $item['price']);
                mysqli_stmt_execute($stmt_item);

                mysqli_stmt_bind_param($stmt_stock, "ii", $item['quantity'], $product_id);
                mysqli_stmt_execute($stmt_stock);
            }

            // If all queries were successful, commit the transaction
            mysqli_commit($conn);

            // 3. Clear the shopping cart and redirect to a success page
            unset($_SESSION['cart']);
            header('Location: ../order_success.php?order_id=' . $order_id);
            exit();

        } catch (mysqli_sql_exception $exception) {
            // If any query fails, roll back the transaction
            mysqli_rollback($conn);
            die("Database error. Your order could not be saved. Please contact support.");
        }
    } else {
        // Payment was not successful
        header('Location: ../checkout.php'); // Or a payment failure page
        exit();
    }

} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}
?>
