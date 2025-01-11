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
    case 'get-therapist-applications':
        $response = getTherapistApplications($DB);
        break;
    case 'update-application-status':
        $response = updateApplicationStatus($DB, $data);
        break;
    case 'get-pending-count':
        $response = getPendingApplicationsCount($DB);
        break;
    default:
        $response['error'] = "Invalid method: " . $METHOD;
        $response['success'] = false;
}

echo json_encode($response);

// Get all therapist applications
function getTherapistApplications($DB) {
    $response = ['success' => false];

    try {
        // Get pending count first
        $countStmt = $DB->prepare("
            SELECT COUNT(*) as pending_count 
            FROM therapist_applications 
            WHERE application_status = 'pending'
        ");
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $pendingCount = $countResult->fetch_assoc()['pending_count'];

        $stmt = $DB->prepare("
            SELECT 
                ta.*,
                u.first_name,
                u.last_name,
                u.email,
                u.address,
                u.phone_number,
                u.user_img
            FROM therapist_applications ta
            JOIN users u ON u.id = ta.user_id
            ORDER BY ta.created_at DESC
        ");

        $stmt->execute();
        $result = $stmt->get_result();
        $applications = $result->fetch_all(MYSQLI_ASSOC);

        // Format the response
        foreach ($applications as &$application) {
            // Get specialties for this application
            $specialtiesStmt = $DB->prepare("
                SELECT s.id, s.name
                FROM specialties s
                JOIN therapist_specialties ts ON ts.specialty_id = s.id
                JOIN therapist_details td ON td.id = ts.therapist_id
                WHERE td.user_id = ?
            ");
            
            $specialtiesStmt->bind_param("i", $application['user_id']);
            $specialtiesStmt->execute();
            $specialtiesResult = $specialtiesStmt->get_result();
            $application['specialties'] = $specialtiesResult->fetch_all(MYSQLI_ASSOC);

            // Convert file paths to URLs
            $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . "/Therapify/";
            
            $application['cv_file'] = $baseUrl . $application['cv_file'];
            $application['diploma_file'] = $baseUrl . $application['diploma_file'];
            $application['license_file'] = $baseUrl . $application['license_file'];

            $application['user'] = [
                'id' => $application['user_id'],
                'first_name' => $application['first_name'],
                'last_name' => $application['last_name'],
                'email' => $application['email'],
                'address' => $application['address'],
                'phone_number' => $application['phone_number'],
                'user_img' => $application['user_img']
            ];

            // Remove redundant user fields
            unset($application['first_name']);
            unset($application['last_name']);
            unset($application['email']);
            unset($application['address']);
            unset($application['phone_number']);
            unset($application['user_img']);
        }

        $response['success'] = true;
        $response['applications'] = $applications;
        $response['pendingCount'] = $pendingCount;
    } catch (Exception $e) {
        $response['error'] = "Failed to fetch applications: " . $e->getMessage();
    }

    return $response;
}

// Update application status
function updateApplicationStatus($DB, $data) {
    $response = ['success' => false];

    if (!isset($data['application_id']) || !isset($data['status']) || !isset($data['admin_notes'])) {
        $response['error'] = "Missing required fields";
        return $response;
    }

    try {
        $DB->begin_transaction();

        // Get application details first
        $stmt = $DB->prepare("
            SELECT ta.*, u.user_role
            FROM therapist_applications ta
            JOIN users u ON u.id = ta.user_id
            WHERE ta.id = ?
        ");
        $stmt->bind_param("i", $data['application_id']);
        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();

        if (!$application) {
            throw new Exception("Application not found");
        }

        // Update application status
        $stmt = $DB->prepare("
            UPDATE therapist_applications 
            SET application_status = ?, admin_notes = ?, updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ssi",
            $data['status'],
            $data['admin_notes'],
            $data['application_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update application status");
        }

        // If approved, update user role to therapist and ensure therapist details exist
        if ($data['status'] === 'approved') {
            // Update user role if not already a therapist
            if ($application['user_role'] !== 'therapist') {
                $stmt = $DB->prepare("
                    UPDATE users 
                    SET user_role = 'therapist'
                    WHERE id = ?
                ");
                $stmt->bind_param("i", $application['user_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update user role");
                }
            }

            // Check if therapist details already exist
            $stmt = $DB->prepare("
                SELECT id FROM therapist_details WHERE user_id = ?
            ");
            $stmt->bind_param("i", $application['user_id']);
            $stmt->execute();
            $existingDetails = $stmt->get_result()->fetch_assoc();

            // If no details exist, create them with default values
            if (!$existingDetails) {
                $stmt = $DB->prepare("
                    INSERT INTO therapist_details (
                        user_id, title, about_text, session_fee,
                        session_duration, languages_spoken,
                        video_session_available, face_to_face_session_available
                    ) VALUES (?, '', '', 0, 50, '[]', 1, 1)
                ");
                $stmt->bind_param("i", $application['user_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create therapist details");
                }
            }
        }

        $DB->commit();
        $response['success'] = true;
        $response['message'] = "Application status updated successfully";
    } catch (Exception $e) {
        $DB->rollback();
        $response['error'] = "Failed to update application status: " . $e->getMessage();
    }

    return $response;
}

// Get pending applications count
function getPendingApplicationsCount($DB) {
    $response = ['success' => false];

    try {
        $stmt = $DB->prepare("
            SELECT COUNT(*) as pending_count 
            FROM therapist_applications 
            WHERE application_status = 'pending'
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['pending_count'];

        $response['success'] = true;
        $response['pendingCount'] = $count;
    } catch (Exception $e) {
        $response['error'] = "Failed to fetch pending count: " . $e->getMessage();
    }

    return $response;
} 