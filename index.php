<?php
session_start();
require_once 'php/db_connect.php';

// Fetch approved reviews from the database
$reviews = [];
$sql_reviews = "SELECT client_name, rating, comment FROM reviews WHERE is_approved = TRUE ORDER BY created_at DESC LIMIT 3";
$result_reviews = mysqli_query($conn, $sql_reviews);
if ($result_reviews) {
    while ($row = mysqli_fetch_assoc($result_reviews)) {
        $reviews[] = $row;
    }
}

// Fetch featured products from the database
$products = [];
$sql_products = "SELECT product_id, name, price, image_url FROM products WHERE stock_quantity > 0 ORDER BY created_at DESC LIMIT 4";
$result_products = mysqli_query($conn, $sql_products);
if ($result_products) {
    while ($row = mysqli_fetch_assoc($result_products)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to VetSmart Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-light-blue: #5AC8FA;
            --ios-green: #34C759;
            --ios-orange: #FF9500;
            --ios-red: #FF3B30;
            --ios-purple: #AF52DE;
            --ios-pink: #FF2D92;
            --ios-yellow: #FFCC00;
            --ios-gray: #8E8E93;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --ios-white: #FFFFFF;
            --ios-black: #000000;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.15);
            --shadow-heavy: 0 8px 30px rgba(0,0,0,0.2);
            --blur-bg: rgba(255,255,255,0.8);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #F2F2F7 0%, #E5E5EA 100%);
            color: var(--ios-dark-gray);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* iOS-style Glassmorphism Navigation */
        .navbar {
            background: var(--blur-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 0.5px solid rgba(0,0,0,0.1);
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--ios-blue) !important;
            text-decoration: none;
        }

        .nav-link {
            color: var(--ios-dark-gray) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 8px;
            padding: 8px 16px !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--ios-blue) !important;
            background: rgba(0,122,255,0.1);
            transform: translateY(-1px);
        }

        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-light);
            position: relative;
            overflow: hidden;
        }

        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover:before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue));
        }

        .btn-success {
            background: linear-gradient(135deg, var(--ios-green), #30D158);
        }

        .btn-outline-success {
            border: 2px solid var(--ios-green);
            color: var(--ios-green);
            background: transparent;
        }

        .btn-outline-success:hover {
            background: var(--ios-green);
        }

        .btn-outline-danger {
            border: 2px solid var(--ios-red);
            color: var(--ios-red);
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: var(--ios-red);
        }

        /* Hero Section with Video Background */
        .hero-section {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }

        .hero-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -2;
            transform: translateX(-50%) translateY(-50%);
            background-size: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            animation: slideInUp 1s ease-out;
        }

        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: slideInUp 1s ease-out 0.2s both;
        }

        .hero-section .btn {
            margin: 0 10px 10px 0;
            animation: slideInUp 1s ease-out 0.4s both;
        }

        /* iOS-style Cards */
        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            background: var(--ios-white);
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .card-img-top {
            transition: transform 0.3s ease;
            height: 200px;
            object-fit: cover;
        }

        .card:hover .card-img-top {
            transform: scale(1.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            color: var(--ios-dark-gray);
            margin-bottom: 0.5rem;
        }

        /* Featured Products Section */
        .featured-products {
            padding: 80px 0;
            background: var(--ios-light-gray);
        }

        .featured-products h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--ios-dark-gray);
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease-out;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 80px 0;
            background: var(--ios-white);
        }

        .testimonials-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--ios-dark-gray);
            margin-bottom: 3rem;
        }

        .testimonials-section .card {
            background: linear-gradient(135deg, #F2F2F7, #FFFFFF);
            height: 100%;
        }

        .text-warning {
            color: var(--ios-yellow) !important;
        }

        /* iOS-style Modal */
        .modal-content {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-heavy);
            backdrop-filter: blur(20px);
        }

        .modal-header {
            border-bottom: 1px solid var(--ios-light-gray);
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 700;
            color: var(--ios-dark-gray);
        }

        .form-control {
            border: 2px solid var(--ios-light-gray);
            border-radius: var(--border-radius);
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--ios-blue);
            box-shadow: 0 0 0 3px rgba(0,122,255,0.1);
        }

        /* Chatbot with iOS styling */
        #chat-icon {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue));
            color: white;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: var(--shadow-medium);
            z-index: 1000;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        #chat-icon:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-heavy);
        }

        #chat-window {
            position: fixed;
            bottom: 110px;
            right: 30px;
            width: 380px;
            max-height: 500px;
            background: var(--blur-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-heavy);
            display: none;
            flex-direction: column;
            z-index: 1000;
            border: 1px solid rgba(255,255,255,0.2);
            animation: slideInUp 0.3s ease-out;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue));
            color: white;
            padding: 20px;
            border-top-left-radius: var(--border-radius-lg);
            border-top-right-radius: var(--border-radius-lg);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .chat-body {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            max-height: 300px;
        }

        .chat-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.5);
            border-bottom-left-radius: var(--border-radius-lg);
            border-bottom-right-radius: var(--border-radius-lg);
        }

        .chat-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 18px;
            margin-bottom: 12px;
            line-height: 1.4;
            animation: messageSlide 0.3s ease-out;
            word-wrap: break-word;
        }

        .user-bubble {
            background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue));
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        .bot-bubble {
            background: var(--ios-light-gray);
            color: var(--ios-dark-gray);
            align-self: flex-start;
        }
        .bot-bubble p { margin-bottom: 0.5rem; }
        .bot-bubble ul, .bot-bubble ol { padding-left: 1.2rem; }
        .bot-bubble li { margin-bottom: 0.25rem; }

        /* Footer */
        footer {
            background: var(--ios-dark-gray) !important;
            color: var(--ios-white) !important;
            padding: 40px 0 !important;
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section p {
                font-size: 1.1rem;
            }
            
            #chat-window {
                width: 90%;
                right: 5%;
            }
            
            .featured-products h2,
            .testimonials-section h2 {
                font-size: 2rem;
            }
        }

        /* Star rating enhancement */
        #ratingStars .fa-star {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        #ratingStars .fa-star:hover {
            transform: scale(1.2);
        }

        /* Loading states */
        .btn:disabled {
            opacity: 0.6;
            transform: none !important;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Section spacing */
        section {
            position: relative;
        }

        /* Enhanced focus states */
        .btn:focus,
        .form-control:focus {
            outline: none;
        }
    </style>
</head>
<body>

    <!-- Header & Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dog me-2"></i>VetSmart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <?php $dashboard_link = $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'client/dashboard.php'; ?>
                        <li class="nav-item"><a href="<?php echo $dashboard_link; ?>" class="btn btn-primary ms-lg-3">Dashboard</a></li>
                        <li class="nav-item"><a href="php/logout.php" class="btn btn-outline-danger ms-lg-2">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="btn btn-outline-success ms-lg-3">Login</a></li>
                        <li class="nav-item"><a href="register.php" class="btn btn-success ms-lg-2">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-overlay"></div>
        <video playsinline autoplay muted loop class="hero-video">
            <source src="assets/videos/98821-650523129.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="container hero-content">
            <h1 class="display-4">Compassionate Care for Your Beloved Pets</h1>
            <p class="lead">Your pet's health is our top priority. We provide expert care with a gentle touch.</p>
            <a href="predict.php" class="btn btn-light btn-lg">
                <i class="fas fa-paw me-2"></i>Symptom Prediction
            </a>
            <a href="client/book_appointment.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-calendar-plus me-2"></i>Book an Appointment
            </a>
        </div>
    </header>

    <!-- Featured Products Section -->
    <section class="featured-products">
        <div class="container text-center">
            <h2 class="Fb-5">
                <i class="fas fa-star me-3" style="color: var(--ios-yellow);"></i>
                Featured Products
            </h2>
            <div class="row">
                <?php if (empty($products)): ?>
                    <div class="col-12">
                        <div class="card mx-auto" style="max-width: 400px;">
                            <div class="card-body text-center">
                                <i class="fas fa-box-open fa-3x mb-3" style="color: var(--ios-gray);"></i>
                                <p class="mb-0">No products available at the moment.</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $index => $product): ?>
                    <div class="col-md-6 col-lg-3 mb-4" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text fs-5 fw-bold text-success">
                                    <i class="fas fa-rupee-sign"></i> <?php echo number_format($product['price'], 2); ?>
                                </p>
                                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary mt-auto">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container text-center">
            <h2 class="mb-5">
                <i class="fas fa-quote-left me-3" style="color: var(--ios-purple);"></i>
                What Our Clients Say
            </h2>
            <div class="row">
                <?php if (empty($reviews)): ?>
                    <div class="col-12">
                        <div class="card mx-auto" style="max-width: 400px;">
                            <div class="card-body text-center">
                                <i class="fas fa-comments fa-3x mb-3" style="color: var(--ios-gray);"></i>
                                <p class="mb-0">No reviews yet. Be the first to leave one!</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $index => $review): ?>
                    <div class="col-md-4 mb-4" style="animation-delay: <?php echo $index * 0.2; ?>s;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-quote-left fa-2x" style="color: var(--ios-light-blue); opacity: 0.3;"></i>
                                </div>
                                <p class="card-text">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                                <div class="text-warning mb-3">
                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                                    <?php for ($i = $review['rating']; $i < 5; $i++): ?><i class="far fa-star"></i><?php endfor; ?>
                                </div>
                                <footer class="blockquote-footer">
                                    <strong><?php echo htmlspecialchars($review['client_name']); ?></strong>
                                </footer>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-lg mt-4" data-bs-toggle="modal" data-bs-target="#reviewModal">
                <i class="fas fa-pen-alt me-2"></i>Leave a Review
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">
                <i class="fas fa-copyright me-2"></i>
                2025 VetSmart Hospital. All Rights Reserved.
            </p>
        </div>
    </footer>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-star me-2" style="color: var(--ios-yellow);"></i>
                        Share Your Feedback
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="php/process_feedback.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="clientName" class="form-label">
                                <i class="fas fa-user me-2"></i>Your Name
                            </label>
                            <input type="text" class="form-control" id="clientName" name="client_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-star me-2"></i>Your Rating
                            </label>
                            <div id="ratingStars" class="text-warning fs-4" style="cursor: pointer;">
                                <i class="far fa-star" data-value="1"></i>
                                <i class="far fa-star" data-value="2"></i>
                                <i class="far fa-star" data-value="3"></i>
                                <i class="far fa-star" data-value="4"></i>
                                <i class="far fa-star" data-value="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="ratingValue" required>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">
                                <i class="fas fa-comment me-2"></i>Your Comment
                            </label>
                            <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit_review" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Chatbot Icon -->
    <div id="chat-icon">
        <i class="fas fa-comment-dots"></i>
    </div>

    <!-- Chatbot Window -->
    <div id="chat-window">
        <div class="chat-header">
            <i class="fas fa-robot me-2"></i>VetSmart Assistant
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-bubble bot-bubble">
                <i class="fas fa-paw me-2"></i>
                Hello! I'm VetBot. How can I help you today?
            </div>
        </div>
        <div class="chat-footer">
            <form id="chat-form" class="d-flex">
                <input type="text" id="chat-input" class="form-control" placeholder="Ask about pet care..." required>
                <button type="submit" class="btn btn-success ms-2">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Marked.js and DOMPurify for rendering Markdown -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- GEMINI CHATBOT LOGIC (UPDATED) ---
        const chatIcon = document.getElementById('chat-icon');
        const chatWindow = document.getElementById('chat-window');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatBody = document.getElementById('chatBody');
        const sendButton = chatForm.querySelector('button[type="submit"]');

        chatIcon.addEventListener('click', () => {
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
        });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userMessage = chatInput.value.trim();
            if (!userMessage) return;

            appendMessage(userMessage, 'user-bubble', true);
            chatInput.value = '';
            chatInput.disabled = true;
            sendButton.disabled = true;

            const typingIndicator = createTypingIndicator();
            chatBody.appendChild(typingIndicator);
            chatBody.scrollTop = chatBody.scrollHeight;

            try {
                const apiKey = "AIzaSyBRriChmJMAKRoaBH6ab2nl-iFtxNg4RB4"; // API key is handled by the execution environment
                const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${apiKey}`;

                const systemPrompt = "You are a friendly and knowledgeable AI assistant for VetSmart, a veterinary hospital. Your name is VetBot. Your goal is to provide helpful, general information about dog and cat care. You can use Markdown for formatting like lists, bolding, and italics. IMPORTANT: You must NEVER provide a medical diagnosis. For any question about a pet's specific illness, symptoms, or emergency, you MUST strongly advise the user to consult a professional veterinarian at the clinic immediately. Keep your answers concise and easy to understand.";

                const payload = {
                    contents: [{ parts: [{ text: userMessage }] }],
                    systemInstruction: { parts: [{ text: systemPrompt }] },
                };

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    throw new Error(`API Error: ${response.statusText}`);
                }

                const result = await response.json();
                const botResponse = result.candidates?.[0]?.content?.parts?.[0]?.text || "I'm sorry, I couldn't process that. Please try asking in a different way.";
                
                typingIndicator.remove();
                appendMessage(botResponse, 'bot-bubble', false);

            } catch (error) {
                console.error('Gemini API Error:', error);
                typingIndicator.remove();
                appendMessage('Sorry, I seem to be having trouble connecting. Please check your connection and try again.', 'bot-bubble', true);
            } finally {
                chatInput.disabled = false;
                sendButton.disabled = false;
                chatInput.focus();
            }
        });

        function appendMessage(message, bubbleClass, isPlainText) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('chat-bubble', bubbleClass);
            
            if (isPlainText) {
                messageDiv.textContent = message;
            } else {
                // Render Markdown content safely
                const dirtyHtml = marked.parse(message);
                messageDiv.innerHTML = DOMPurify.sanitize(dirtyHtml);
            }
            
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function createTypingIndicator() {
            const indicator = document.createElement('div');
            indicator.classList.add('chat-bubble', 'bot-bubble');
            indicator.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Typing...</span></div>';
            return indicator;
        }

        // --- OTHER PAGE LOGIC (REVIEWS, ANIMATIONS, ETC.) ---

        const stars = document.querySelectorAll('#ratingStars .fa-star');
        const ratingValueInput = document.getElementById('ratingValue');
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = star.getAttribute('data-value');
                ratingValueInput.value = value;
                stars.forEach((s, index) => {
                    s.classList.toggle('fas', index < value);
                    s.classList.toggle('far', index >= value);
                });
            });
        });

        let lastScrollTop = 0;
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            lastScrollTop = scrollTop;
        });
    }); 

    // Add CSS for animations and effects
    const additionalStyles = `
        .ripple { position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.6); pointer-events: none; animation: ripple-animation 0.6s linear; }
        @keyframes ripple-animation { to { transform: scale(2); opacity: 0; } }
        .navbar { transition: transform 0.3s ease; }
        .animate-fade-in { animation: fadeInUp 1s ease-out; }
    `;
    const styleSheet = document.createElement('style');
    styleSheet.textContent = additionalStyles;
    document.head.appendChild(styleSheet);
    </script>
</body>
</html>