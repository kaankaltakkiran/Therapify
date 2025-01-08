<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

// JWT constants
define('JWT_SECRET_KEY', 'your-256-bit-secret'); // Use a secure secret key in production
define('JWT_EXPIRE_TIME', 3600); // Token validity period (in seconds, 1 hour)

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Database connection
require_once 'db_connection.php';

// JWT functions
function generateJWT($user) {
    $issuedAt = time();
    $expire = $issuedAt + JWT_EXPIRE_TIME;

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expire,
        'user_id' => $user['id'],
        'email' => $user['email'],
        'user_role' => $user['user_role']
    ];

    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64_encode(json_encode($payload));
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET_KEY, true));

    return "$header.$payload.$signature";
}

function verifyJWT($token) {
    try {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return false;
        }

        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature = $tokenParts[2];

        $valid = base64_encode(hash_hmac('sha256', "$tokenParts[0].$tokenParts[1]", JWT_SECRET_KEY, true)) === $signature;
        if (!$valid) {
            return false;
        }

        $payload = json_decode($payload, true);
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    } catch (Exception $e) {
        return false;
    }
}

// Read raw input data
$data = json_decode(file_get_contents("php://input"), true);
$METHOD = $data['method'] ?? '';

$response = ['success' => false];

// Database connection
$db = new Database();
$DB = $db->connection;

// Protected endpoints
$protected_methods = ['get-profile', 'update-profile', 'delete-profile'];
if (in_array($METHOD, $protected_methods)) {
    $headers = getallheaders();
    $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

    if (!$token || !($payload = verifyJWT($token))) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
        exit();
    }
    $data['user_id'] = $payload['user_id'];
}

// Route methods
switch ($METHOD) {
    case 'register':
        $response = registerUser($DB, $data);
        break;

    case 'therapist-register':
        $response = registerTherapist($DB, $data);
        break;

    case 'login':
        $response = loginUser($DB, $data);
        break;

    default:
        $response['error'] = "Invalid method";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$db->closeConnection();

// User registration
function registerUser($DB, $data) {
    $response = ['success' => false];

    try {
        // Begin transaction
        $DB->begin_transaction();

        // Check if email already exists
        $stmt = $DB->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];

        if ($count > 0) {
            $response['error'] = "This email is already in use";
            $DB->rollback();
            return $response;
        }

        // Hash password
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert user
        $stmt = $DB->prepare("
            INSERT INTO users (
                first_name, last_name, email, address, phone_number, 
                birth_of_date, user_img, password, user_role
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'user')
        ");

        $stmt->bind_param(
            "ssssssss",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['address'],
            $data['phone_number'],
            $data['birth_of_date'],
            $data['user_img'],
            $hash
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "User registered successfully";
            $response['user_id'] = $stmt->insert_id;
            $DB->commit();
        } else {
            $response['error'] = "User registration failed";
            $DB->rollback();
        }
    } catch (Exception $e) {
        $DB->rollback();
        $response['error'] = "Database error: " . $e->getMessage();
    }

    return $response;
}

// Therapist registration
function registerTherapist($DB, $data) {
    $response = ['success' => false];

    try {
        // Begin transaction
        $DB->begin_transaction();

        // Check if email already exists
        $stmt = $DB->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];

        if ($count > 0) {
            $response['error'] = "This email is already in use";
            $DB->rollback();
            return $response;
        }

        // Hash password
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert user with therapist role
        $stmt = $DB->prepare("
            INSERT INTO users (
                first_name, last_name, email, address, phone_number, 
                birth_of_date, user_img, password, user_role
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'therapist')
        ");

        $stmt->bind_param(
            "ssssssss",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['address'],
            $data['phone_number'],
            $data['birth_of_date'],
            $data['user_img'],
            $hash
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create user account");
        }

        $userId = $stmt->insert_id;

        // Create therapist application
        $stmt = $DB->prepare("
            INSERT INTO therapist_applications (
                user_id, education, license_number, experience_years,
                cv_file, diploma_file, license_file
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ississs",
            $userId,
            $data['education'],
            $data['license_number'],
            $data['experience_years'],
            $data['cv_file'],
            $data['diploma_file'],
            $data['license_file']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create therapist application");
        }

        // Create therapist details
        $stmt = $DB->prepare("
            INSERT INTO therapist_details (
                user_id, title, about_text, session_fee, session_duration,
                languages_spoken, video_session_available,
                face_to_face_session_available, office_address
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $languagesJson = json_encode($data['languages_spoken']);

        $stmt->bind_param(
            "issdisiss",
            $userId,
            $data['title'],
            $data['about_text'],
            $data['session_fee'],
            $data['session_duration'],
            $languagesJson,
            $data['video_session_available'],
            $data['face_to_face_session_available'],
            $data['office_address']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create therapist details");
        }

        // Insert specialties
        $therapistDetailsId = $stmt->insert_id;
        foreach ($data['specialties'] as $specialtyId) {
            $stmt = $DB->prepare("
                INSERT INTO therapist_specialties (therapist_id, specialty_id)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $therapistDetailsId, $specialtyId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to add specialty");
            }
        }

        $response['success'] = true;
        $response['message'] = "Therapist registration successful. Your application is pending review.";
        $response['user_id'] = $userId;
        $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        $response['error'] = "Registration failed: " . $e->getMessage();
    }

    return $response;
}

// User login
function loginUser($DB, $data) {
    $response = ['success' => false];

    try {
        $stmt = $DB->prepare("
            SELECT id, first_name, last_name, email, password, user_role, user_img
            FROM users 
            WHERE email = ?
        ");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($data['password'], $user['password'])) {
                // Remove sensitive data
                unset($user['password']);

                // Generate JWT token
                $token = generateJWT($user);

                // Get additional data based on user role
                if ($user['user_role'] === 'therapist') {
                    // Get therapist details
                    $stmt = $DB->prepare("
                        SELECT td.*, ta.application_status
                        FROM therapist_details td
                        LEFT JOIN therapist_applications ta ON ta.user_id = td.user_id
                        WHERE td.user_id = ?
                    ");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $therapistDetails = $stmt->get_result()->fetch_assoc();
                    
                    if ($therapistDetails) {
                        $user['therapist_details'] = $therapistDetails;
                        
                        // Get specialties
                        $stmt = $DB->prepare("
                            SELECT s.id, s.name
                            FROM specialties s
                            JOIN therapist_specialties ts ON ts.specialty_id = s.id
                            WHERE ts.therapist_id = ?
                        ");
                        $stmt->bind_param("i", $therapistDetails['id']);
                        $stmt->execute();
                        $specialties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $user['therapist_details']['specialties'] = $specialties;
                    }
                }

                $response['success'] = true;
                $response['message'] = "Logged in successfully";
                $response['token'] = $token;
                $response['user'] = $user;
            } else {
                $response['error'] = "Invalid email or password";
            }
        } else {
            $response['error'] = "User not found";
        }
    } catch (Exception $e) {
        $response['error'] = "Login failed: " . $e->getMessage();
    }

    return $response;
}
