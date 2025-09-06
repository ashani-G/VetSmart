<?php
// This file handles all AJAX requests for the application.
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json'); // Set header to return JSON

// Check for the action parameter to route the request
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'check_symptoms':
            handle_check_symptoms($conn);
            break;
        case 'get_chatbot_response':
            handle_chatbot_response($conn);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No action specified.']);
}

/**
 * Handles the symptom checker logic.
 * @param mysqli $conn The database connection object.
 */
function handle_check_symptoms($conn) {
    if (!isset($_POST['symptoms']) || !is_array($_POST['symptoms']) || empty($_POST['symptoms'])) {
        echo json_encode(['success' => false, 'message' => 'No symptoms provided.']);
        exit;
    }

    $selected_symptoms = $_POST['symptoms'];
    $placeholders = implode(',', array_fill(0, count($selected_symptoms), '?'));
    $types = str_repeat('i', count($selected_symptoms));

    // SQL query to find and rank illnesses based on matching symptoms
    $sql = "SELECT 
                i.illness_id, 
                i.illness_name, 
                i.recommendation,
                COUNT(s.symptom_id) AS match_count
            FROM symptom_illness_map sim
            JOIN illnesses i ON sim.illness_id = i.illness_id
            JOIN symptoms s ON sim.symptom_id = s.symptom_id
            WHERE sim.symptom_id IN ($placeholders)
            GROUP BY i.illness_id, i.illness_name, i.recommendation
            ORDER BY match_count DESC
            LIMIT 5";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$selected_symptoms);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $illnesses = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $illnesses[] = $row;
        }
    }

    echo json_encode(['success' => true, 'illnesses' => $illnesses]);
    exit;
}

/**
 * Handles the chatbot logic.
 * @param mysqli $conn The database connection object.
 */
function handle_chatbot_response($conn) {
    if (!isset($_POST['message']) || empty($_POST['message'])) {
        echo json_encode(['success' => false, 'response' => 'No message provided.']);
        exit;
    }

    $user_message = strtolower(mysqli_real_escape_string($conn, $_POST['message']));
    $words = preg_split('/[\s,]+/', $user_message, -1, PREG_SPLIT_NO_EMPTY); // Split by space or comma
    $default_response = "I'm sorry, I don't have information about that. Our standard services include appointments, wellness exams, and emergency care. How can I help with one of those?";

    // Build a query to find matching keywords
    $conditions = [];
    foreach ($words as $word) {
        if (strlen($word) > 2) { // Ignore very short words to improve relevance
            $conditions[] = "keywords LIKE '%" . $word . "%'";
        }
    }

    if (empty($conditions)) {
        echo json_encode(['success' => true, 'response' => $default_response]);
        exit;
    }

    $sql = "SELECT response FROM chatbot_responses WHERE " . implode(' OR ', $conditions) . " LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'response' => $row['response']]);
    } else {
        echo json_encode(['success' => true, 'response' => $default_response]);
    }
    exit;
}
?>
