<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Database connection
require_once 'db_connection.php';

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$METHOD = $data['method'] ?? '';

$response = ['success' => false];

// Database connection
$db = new Database();
$DB = $db->connection;

// Route methods
switch ($METHOD) {
    case 'get-approved-therapists':
        $response = getApprovedTherapists($DB);
        break;
    default:
        $response['error'] = "Invalid method: " . $METHOD;
        $response['success'] = false;
}

echo json_encode($response);

// Get all approved therapists with their details
function getApprovedTherapists($DB) {
    $response = ['success' => false];

    try {
        $stmt = $DB->prepare("
            SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                u.address,
                u.user_img,
                td.*,
                ta.application_status
            FROM users u
            JOIN therapist_details td ON td.user_id = u.id
            JOIN (
                SELECT user_id, application_status
                FROM therapist_applications
                WHERE application_status = 'approved'
                AND id IN (
                    SELECT MAX(id)
                    FROM therapist_applications
                    GROUP BY user_id
                )
            ) ta ON ta.user_id = u.id
            WHERE u.user_role = 'therapist'
        ");

        $stmt->execute();
        $result = $stmt->get_result();
        $therapists = $result->fetch_all(MYSQLI_ASSOC);

        // Get specialties for each therapist
        foreach ($therapists as &$therapist) {
            $stmt = $DB->prepare("
                SELECT s.id, s.name
                FROM specialties s
                JOIN therapist_specialties ts ON ts.specialty_id = s.id
                WHERE ts.therapist_id = ?
            ");
            
            $stmt->bind_param("i", $therapist['id']);
            $stmt->execute();
            $specialties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $therapist['specialties'] = $specialties;

            // Format image URL
            if ($therapist['user_img']) {
                $filename = basename($therapist['user_img']);
                $therapist['user_img'] = "uploads/profile_images/" . $filename;
            }

            // Parse languages_spoken from JSON string to array
            if ($therapist['languages_spoken']) {
                $therapist['languages_spoken'] = json_decode($therapist['languages_spoken'], true);
            } else {
                $therapist['languages_spoken'] = [];
            }
        }

        $response['success'] = true;
        $response['therapists'] = $therapists;
    } catch (Exception $e) {
        $response['error'] = "Failed to fetch therapists: " . $e->getMessage();
    }

    return $response;
} 