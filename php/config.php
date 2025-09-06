<?php
// This file contains the configuration for the Stripe payment gateway.

// It's crucial to load the Composer autoloader to use the Stripe library.
require_once __DIR__ . '/../vendor/autoload.php';

// --- Stripe API Keys ---
// Replace these with your actual keys from your Stripe Dashboard.
// For testing, use your "Test mode" keys.
$stripe_secret_key = 'sk_test_51QAT2kBM7CfBS2EHlj9VYRggCCtk8CJ8jGLjqmpj1ckd2E9HNJCIxJyVH9NPUUqdCqYoP9x3uPKTb35nZks8ujPV00PnqXkG6D'; // Keep this secret!
$stripe_publishable_key = 'pk_test_51QAT2kBM7CfBS2EHVtxixxcu2Qnpwx0zqky9I5S0GYcAohCIjMfI0aV8PzbuuQdc890Y06XHhLJPCK4QkdCLlbiK00ONkc4TpN';

// Initialize the Stripe PHP library
\Stripe\Stripe::setApiKey($stripe_secret_key);
?>
