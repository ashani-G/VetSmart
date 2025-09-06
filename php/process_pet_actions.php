<?php
// Start the session and include necessary files.
session_start();
require_once 'db_connect.php';

// --- Security Check: Ensure user is logged in ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "You must be logged in to manage pets.";
    $_SESSION['msg_type'] = "danger";
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// --- UPLOAD DIRECTORY ---
$upload_dir = '../uploads/pets/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// --- Helper function to handle file upload ---
function handle_pet_image_upload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $file = $_FILES[$file_input_name];
        $file_name = basename($file['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file type
        if (!in_array($file_ext, $allowed_exts)) {
            $_SESSION['message'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            $_SESSION['msg_type'] = "danger";
            return null;
        }

        // Create a unique name and move the file
        $new_file_name = uniqid('', true) . '.' . $file_ext;
        $target_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return 'uploads/pets/' . $new_file_name; // Return the path to store in DB
        } else {
            $_SESSION['message'] = "Failed to upload image.";
            $_SESSION['msg_type'] = "danger";
            return null;
        }
    }
    return null; // No new file was uploaded
}


// --- ADD PET LOGIC ---
if (isset($_POST['add_pet'])) {
    $pet_name = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $species = mysqli_real_escape_string($conn, $_POST['species']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $medical_history = mysqli_real_escape_string($conn, $_POST['medical_history']);

    // Handle image upload
    $pet_image_url = handle_pet_image_upload('pet_image', $upload_dir);

    if (empty($pet_name) || empty($species)) {
        $_SESSION['message'] = "Pet Name and Species are required.";
        $_SESSION['msg_type'] = "danger";
    } else {
        $sql = "INSERT INTO pets (owner_id, pet_name, species, breed, date_of_birth, gender, medical_history, pet_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssssss", $user_id, $pet_name, $species, $breed, $date_of_birth, $gender, $medical_history, $pet_image_url);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Pet successfully added!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: Could not add pet.";
            $_SESSION['msg_type'] = "danger";
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: ../client/my_pets.php");
    exit();
}

// --- UPDATE PET LOGIC ---
if (isset($_POST['update_pet'])) {
    $pet_id = intval($_POST['pet_id']);
    $pet_name = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $species = mysqli_real_escape_string($conn, $_POST['species']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $medical_history = mysqli_real_escape_string($conn, $_POST['medical_history']);
    
    // Fetch old image URL to delete if a new one is uploaded
    $sql_old_img = "SELECT pet_image_url FROM pets WHERE pet_id = ? AND owner_id = ?";
    $stmt_old_img = mysqli_prepare($conn, $sql_old_img);
    mysqli_stmt_bind_param($stmt_old_img, "ii", $pet_id, $user_id);
    mysqli_stmt_execute($stmt_old_img);
    $result_old_img = mysqli_stmt_get_result($stmt_old_img);
    $old_image_data = mysqli_fetch_assoc($result_old_img);
    $old_image_path = $old_image_data ? $old_image_data['pet_image_url'] : null;
    mysqli_stmt_close($stmt_old_img);

    $pet_image_url = handle_pet_image_upload('pet_image', $upload_dir);

    if ($pet_image_url && $old_image_path && file_exists('../' . $old_image_path)) {
        unlink('../' . $old_image_path); // Delete old image
    }

    $sql = "UPDATE pets SET pet_name = ?, species = ?, breed = ?, date_of_birth = ?, gender = ?, medical_history = ?" . ($pet_image_url ? ", pet_image_url = ?" : "") . " WHERE pet_id = ? AND owner_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($pet_image_url) {
        mysqli_stmt_bind_param($stmt, "sssssssii", $pet_name, $species, $breed, $date_of_birth, $gender, $medical_history, $pet_image_url, $pet_id, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "ssssssii", $pet_name, $species, $breed, $date_of_birth, $gender, $medical_history, $pet_id, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Pet details updated successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating pet details.";
        $_SESSION['msg_type'] = "danger";
    }
    mysqli_stmt_close($stmt);
    header("Location: ../client/my_pets.php");
    exit();
}

// --- DELETE PET LOGIC ---
if (isset($_POST['delete_pet'])) {
    $pet_id = intval($_POST['pet_id']);
    
    // First, get the image path to delete the file
    $sql_img = "SELECT pet_image_url FROM pets WHERE pet_id = ? AND owner_id = ?";
    $stmt_img = mysqli_prepare($conn, $sql_img);
    mysqli_stmt_bind_param($stmt_img, "ii", $pet_id, $user_id);
    mysqli_stmt_execute($stmt_img);
    $result_img = mysqli_stmt_get_result($stmt_img);
    $image_data = mysqli_fetch_assoc($result_img);
    mysqli_stmt_close($stmt_img);

    // Now, delete the pet record from the database
    $sql = "DELETE FROM pets WHERE pet_id = ? AND owner_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // If DB deletion is successful, delete the image file
        if ($image_data && !empty($image_data['pet_image_url']) && file_exists('../' . $image_data['pet_image_url'])) {
            unlink('../' . $image_data['pet_image_url']);
        }
        $_SESSION['message'] = "Pet profile deleted successfully.";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting pet.";
        $_SESSION['msg_type'] = "danger";
    }
    mysqli_stmt_close($stmt);
    header("Location: ../client/my_pets.php");
    exit();
}

// If no valid action was provided, redirect back.
header("Location: ../client/dashboard.php");
exit();
?>

