<?php
session_start();

// -------------------------------
// Config
// -------------------------------
$python = "python"; // Use "python3" if on Linux/macOS
$script = __DIR__ . "/predict.py"; // Absolute path to predict.py

// -------------------------------
// Helpers
// -------------------------------
function field($name) {
    return isset($_POST[$name]) ? trim($_POST[$name]) : "";
}

function safe($s) {
    return htmlspecialchars($s ?? "", ENT_QUOTES, 'UTF-8');
}

$prediction = null;
$error = null;

// -------------------------------
// Handle form submission
// -------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $required = [
        "species", "age_years", "breed", "is_neutered", "weight_kg", "is_vaccinated",
        "lethargy_level", "appetite_status", "drinking_status", "is_vomiting",
        "is_diarrhea", "has_blood_in_stool", "is_straining_to_urinate",
        "urinating_more_frequently", "has_blood_in_urine", "is_coughing",
        "is_sneezing", "has_nasal_discharge", "is_itching_scratching",
        "has_hair_loss", "is_lame_limping"
    ];

    // Check all fields filled
    foreach ($required as $r) {
        if (field($r) === "") {
            $error = "Please fill in all fields before submitting.";
            break;
        }
    }

    // Check numeric fields
    if (!$error) {
        if (!is_numeric(field("age_years")) || !is_numeric(field("weight_kg"))) {
            $error = "Age (years) and Weight (kg) must be numeric values.";
        }
    }

    // Call Python script
    if (!$error) {
        $args = [];
        foreach ($required as $r) {
            $args[] = escapeshellarg(field($r));
        }
        $cmd = escapeshellcmd($python) . " " . escapeshellarg($script) . " " . implode(" ", $args);
        
        // Execute the command and capture output
        $output = shell_exec($cmd . " 2>&1");

        if ($output === null || strpos(strtolower($output), 'error') !== false) {
            // Check for common issues to provide a more helpful message
            if (strpos($output, 'command not found') !== false) {
                $error = "Prediction failed. The Python command is not configured correctly on the server.";
            } else {
                $error = "An error occurred during prediction. Please ensure the model and script are set up correctly.";
                 // For debugging: error_log("Predictor Output: " . $output);
            }
        } else {
            $prediction = trim($output);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Symptom Checker - VetSmart</title>
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
            --ios-gray: #8E8E93;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --ios-white: #FFFFFF;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.12);
            --blur-bg: rgba(255,255,255,0.8);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: var(--ios-light-gray);
            color: var(--ios-dark-gray);
        }
        .navbar { background: var(--blur-bg) !important; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 0.5px solid rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--ios-blue) !important; }
        .nav-link { color: var(--ios-dark-gray) !important; font-weight: 500; border-radius: 8px; padding: 8px 16px !important; }
        .nav-link:hover, .nav-link.active { color: var(--ios-blue) !important; background: rgba(0,122,255,0.1); }
        .btn { border-radius: var(--border-radius); font-weight: 600; padding: 12px 24px; border: none; transition: all 0.3s ease; box-shadow: var(--shadow-light); }
        .btn:hover { transform: translateY(-2px); box-shadow: var(--shadow-medium); }
        .btn-primary { background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue)); color: white; }
        .btn-success { background: linear-gradient(135deg, var(--ios-green), #30D158); color: white; }
        .page-header { padding: 60px 0; background: var(--ios-white); border-bottom: 1px solid #E5E5EA; }
        .page-header h1 { font-size: 3rem; font-weight: 700; }
        .card { border: none; border-radius: var(--border-radius-lg); background: var(--ios-white); box-shadow: var(--shadow-light); }
        .form-control, .form-select { border-radius: var(--border-radius); border: 2px solid #E5E5EA; padding: 12px 16px; }
        .form-control:focus, .form-select:focus { border-color: var(--ios-blue); box-shadow: 0 0 0 3px rgba(0,122,255,0.1); }
        footer { background: var(--ios-dark-gray) !important; color: var(--ios-white) !important; padding: 40px 0 !important; }
        .accordion-button:not(.collapsed) { color: var(--ios-blue); background-color: #f0f7ff; }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-dog me-2"></i>VetSmart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link active" href="symptom_checker.php">Symptom Checker</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Page Header -->
    <header class="page-header text-center">
        <div class="container">
            <h1 class="display-4">AI Symptom Checker</h1>
            <p class="lead text-muted">Get a preliminary analysis of your pet's symptoms. This tool is not a substitute for professional veterinary advice.</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="row g-5">
            <!-- Form Section -->
            <div class="col-lg-7">
                <div class="card p-4">
                    <h3 class="fw-bold mb-4"><i class="fas fa-file-medical-alt me-2"></i>Enter Pet's Information</h3>
                    <form method="POST" action="">
                        <div class="accordion" id="symptomAccordion">
                            
                            <!-- Section 1: Basic Info -->
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">1. Basic Information</button></h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#symptomAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3"><label class="form-label">Species</label><input type="text" class="form-control" name="species" value="<?= safe(field('species')) ?>" placeholder="e.g., Dog" required></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Breed</label><input type="text" class="form-control" name="breed" value="<?= safe(field('breed')) ?>" placeholder="e.g., Golden Retriever" required></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Age (years)</label><input type="number" step="0.1" class="form-control" name="age_years" value="<?= safe(field('age_years')) ?>" required></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Weight (kg)</label><input type="number" step="0.1" class="form-control" name="weight_kg" value="<?= safe(field('weight_kg')) ?>" required></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: General Health -->
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">2. General Health</button></h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#symptomAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3"><label class="form-label">Neutered/Spayed</label><select class="form-select" name="is_neutered" required><option value="" hidden>Select...</option><option value="Yes" <?= field('is_neutered')==='Yes'?'selected':''; ?>>Yes</option><option value="No" <?= field('is_neutered')==='No'?'selected':''; ?>>No</option></select></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Vaccinated</label><select class="form-select" name="is_vaccinated" required><option value="" hidden>Select...</option><option value="Yes" <?= field('is_vaccinated')==='Yes'?'selected':''; ?>>Yes</option><option value="No" <?= field('is_vaccinated')==='No'?'selected':''; ?>>No</option></select></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Lethargy Level</label><select class="form-select" name="lethargy_level" required><option value="" hidden>Select...</option><option value="Low" <?= field('lethargy_level')==='Low'?'selected':''; ?>>Low</option><option value="Normal" <?= field('lethargy_level')==='Normal'?'selected':''; ?>>Normal</option><option value="High" <?= field('lethargy_level')==='High'?'selected':''; ?>>High</option></select></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Appetite Status</label><select class="form-select" name="appetite_status" required><option value="" hidden>Select...</option><option value="Decreased" <?= field('appetite_status')==='Decreased'?'selected':''; ?>>Decreased</option><option value="Normal" <?= field('appetite_status')==='Normal'?'selected':''; ?>>Normal</option><option value="Increased" <?= field('appetite_status')==='Increased'?'selected':''; ?>>Increased</option></select></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Drinking Status</label><select class="form-select" name="drinking_status" required><option value="" hidden>Select...</option><option value="Decreased" <?= field('drinking_status')==='Decreased'?'selected':''; ?>>Decreased</option><option value="Normal" <?= field('drinking_status')==='Normal'?'selected':''; ?>>Normal</option><option value="Increased" <?= field('drinking_status')==='Increased'?'selected':''; ?>>Increased</option></select></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                             <!-- Section 3: Symptoms -->
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">3. Specific Symptoms</button></h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#symptomAccordion">
                                    <div class="accordion-body">
                                         <div class="row">
                                             <?php
                                             $symptom_fields = [
                                                 "is_vomiting" => "Vomiting", "is_diarrhea" => "Diarrhea", "has_blood_in_stool" => "Blood in Stool",
                                                 "is_straining_to_urinate" => "Straining to Urinate", "urinating_more_frequently" => "Frequent Urination", "has_blood_in_urine" => "Blood in Urine",
                                                 "is_coughing" => "Coughing", "is_sneezing" => "Sneezing", "has_nasal_discharge" => "Nasal Discharge",
                                                 "is_itching_scratching" => "Itching/Scratching", "has_hair_loss" => "Hair Loss", "is_lame_limping" => "Limping/Lameness"
                                             ];
                                             foreach ($symptom_fields as $name => $label):
                                             ?>
                                             <div class="col-md-4 col-6 mb-3">
                                                 <label class="form-label"><?= $label ?></label>
                                                 <select name="<?= $name ?>" class="form-select" required>
                                                     <option value="No" <?= field($name)==='No'?'selected':''; ?>>No</option>
                                                     <option value="Yes" <?= field($name)==='Yes'?'selected':''; ?>>Yes</option>
                                                 </select>
                                             </div>
                                             <?php endforeach; ?>
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">
                            <i class="fas fa-search-plus me-2"></i>Analyze Symptoms
                        </button>
                    </form>
                </div>
            </div>

            <!-- Result Section -->
            <div class="col-lg-5">
                <div class="card p-4 sticky-top" style="top: 120px;">
                    <h3 class="fw-bold mb-3"><i class="fas fa-poll me-2 text-primary"></i>Analysis Result</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><strong>Error:</strong> <?= safe($error) ?></div>
                    <?php elseif ($prediction): ?>
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Preliminary Prediction</h4>
                            <p class="fs-4 fw-bold"><?= safe($prediction) ?></p>
                            <hr>
                            <p class="mb-0"><strong>Disclaimer:</strong> This is an AI-generated prediction and is not a substitute for a professional diagnosis. Please consult with a qualified veterinarian for an accurate assessment and treatment plan.</p>
                        </div>
                         <a href="contact.php" class="btn btn-success mt-3">Book an Appointment</a>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Your pet's potential health issue will be displayed here once you submit the form.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <div class="container">
            <p class="mb-0"><i class="fas fa-copyright me-2"></i>2025 VetSmart Hospital. All Rights Reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>