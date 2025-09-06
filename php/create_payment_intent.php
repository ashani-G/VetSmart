<?php
session_start();
require_once 'config.php'; // Includes Stripe keys and autoloader

// Security check
if (empty($_SESSION['cart'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty.']);
    exit;
}

// Calculate total amount from the session cart
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Stripe expects the amount in the smallest currency unit (e.g., cents)
$amount_in_cents = round($total_amount * 100);

try {
    // Create a PaymentIntent with the order amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount_in_cents,
        'currency' => 'lkr', // Use 'lkr' for Sri Lankan Rupees
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    // Send the client secret back to the browser
    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
    ]);

} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
