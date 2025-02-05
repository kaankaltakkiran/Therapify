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

// Constants for file uploads - Production paths only
define('UPLOAD_BASE_PATH', '/var/www/my_webapp__2/www/api');
define('UPLOAD_BASE_URL', 'https://therapify.kaankaltakkiran.com/api');

// Function to get public URL for uploaded files
function getPublicPath($serverPath) {
    error_log("Getting public path for: " . $serverPath);
    
    if (empty($serverPath)) {
        error_log("Empty server path provided");
        return '';
    }

    // If it's already a full URL, return as is
    if (strpos($serverPath, 'http') === 0) {
        error_log("Path is already a full URL: " . $serverPath);
        return $serverPath;
    }

    // Remove any leading slash for consistency
    $serverPath = ltrim($serverPath, '/');
    error_log("Clean path: " . $serverPath);

    // Construct and return the full URL
    $finalUrl = UPLOAD_BASE_URL . '/' . $serverPath;
    error_log("Final URL: " . $finalUrl);
    return $finalUrl;
}

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
    case 'submit-contact':
        $response = submitContact($data);
        break;
    case 'get-contact-messages':
        $response = getContactMessages();
        break;
    case 'update-contact-status':
        $response = updateContactMessageStatus($data['messageId'], $data['status']);
        break;
    case 'get-unread-messages-count':
        $response = getUnreadMessagesCount();
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

            // Convert file paths to URLs using getPublicPath
            if (!empty($application['cv_file'])) {
                $application['cv_file'] = getPublicPath($application['cv_file']);
            }
            if (!empty($application['diploma_file'])) {
                $application['diploma_file'] = getPublicPath($application['diploma_file']);
            }
            if (!empty($application['license_file'])) {
                $application['license_file'] = getPublicPath($application['license_file']);
            }
            if (!empty($application['user_img'])) {
                $application['user_img'] = getPublicPath($application['user_img']);
            }

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
        }

        $response['success'] = true;
        $response['applications'] = $applications;
        $response['pendingCount'] = $pendingCount;
    } catch (Exception $e) {
        error_log("Failed to fetch applications: " . $e->getMessage());
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

// Submit contact form
function submitContact($data) {
    global $DB;
    
    // Validate required fields
    if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['message'])) {
        return array(
            'success' => false,
            'message' => 'Tüm alanları doldurunuz'
        );
    }
    
    try {
        $stmt = $DB->prepare("
            INSERT INTO contact_messages (first_name, last_name, email, message)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->bind_param('ssss', 
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['message']
        );
        
        if ($stmt->execute()) {
            return array(
                'success' => true,
                'message' => 'Mesajınız başarıyla gönderildi'
            );
        } else {
            throw new Exception('Mesaj kaydedilirken bir hata oluştu');
        }
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'Mesaj gönderilirken bir hata oluştu: ' . $e->getMessage()
        );
    }
}

// Get all contact messages
function getContactMessages() {
    global $DB;
    
    try {
        $stmt = $DB->prepare("
            SELECT * FROM contact_messages 
            ORDER BY created_at DESC
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
        
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        
        return [
            'success' => true,
            'messages' => $messages
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// Update contact message status
function updateContactMessageStatus($messageId, $status) {
    global $DB;
    
    try {
        $stmt = $DB->prepare("
            UPDATE contact_messages 
            SET status = ?
            WHERE id = ?
        ");
        
        $stmt->bind_param("si", $status, $messageId);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Mesaj durumu güncellendi.'
            ];
        } else {
            throw new Exception('Mesaj durumu güncellenirken bir hata oluştu.');
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// Get unread messages count
function getUnreadMessagesCount() {
    global $DB;
    
    try {
        $stmt = $DB->prepare("
            SELECT COUNT(*) as unread_count 
            FROM contact_messages 
            WHERE status = 'new'
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['unread_count'];
        
        return [
            'success' => true,
            'unreadCount' => $count
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
} 