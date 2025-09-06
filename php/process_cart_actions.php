<?php
session_start();
require_once 'db_connect.php';

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- ADD TO CART LOGIC ---
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Validation
    if ($quantity <= 0) {
        $_SESSION['message'] = "Invalid quantity.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../product_detail.php?id=' . $product_id);
        exit();
    }

    // Fetch product from DB to verify stock and get details
    $sql = "SELECT name, price, stock_quantity FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$product) {
        $_SESSION['message'] = "Product not found.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../marketplace.php');
        exit();
    }

    if ($quantity > $product['stock_quantity']) {
        $_SESSION['message'] = "Not enough stock available.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../product_detail.php?id=' . $product_id);
        exit();
    }

    // Add or update product in cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Update quantity if product is already in cart
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        // Add new product to cart
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];
    }

    $_SESSION['message'] = "<strong>" . htmlspecialchars($product['name']) . "</strong> has been added to your cart.";
    $_SESSION['msg_type'] = "success";
    header('Location: ../product_detail.php?id=' . $product_id);
    exit();
}

// --- REMOVE FROM CART LOGIC ---
if (isset($_GET['remove'])) {
    $product_id_to_remove = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$product_id_to_remove])) {
        unset($_SESSION['cart'][$product_id_to_remove]);
        $_SESSION['message'] = "Item removed from cart.";
        $_SESSION['msg_type'] = "warning";
    }
    header('Location: ../cart.php');
    exit();
}


// Redirect if no action is specified
header('Location: ../marketplace.php');
exit();
?>
