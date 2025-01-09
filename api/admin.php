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

        // If approved, update user role to therapist
        if ($data['status'] === 'approved') {
            $stmt = $DB->prepare("
                UPDATE users u
                JOIN therapist_applications ta ON ta.user_id = u.id
                SET u.user_role = 'therapist'
                WHERE ta.id = ?
            ");

            $stmt->bind_param("i", $data['application_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update user role");
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