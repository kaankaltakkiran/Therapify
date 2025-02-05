<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

// JWT constants
define('JWT_SECRET_KEY', 'vypLCUJ3Q8oFj7');
define('JWT_EXPIRE_TIME', 3600);

// Constants for file uploads - Production paths only
define('UPLOAD_BASE_PATH', '/var/www/my_webapp__2/www/api');
define('UPLOAD_BASE_URL', 'https://therapify.kaankaltakkiran.com/api');

// Ensure base upload directory exists and has correct permissions
if (!file_exists(UPLOAD_BASE_PATH)) {
    error_log("Creating base upload directory: " . UPLOAD_BASE_PATH);
    if (!mkdir(UPLOAD_BASE_PATH, 0777, true)) {
        error_log("Failed to create base upload directory: " . UPLOAD_BASE_PATH);
        throw new Exception("Failed to create base upload directory");
    }
    chmod(UPLOAD_BASE_PATH, 0777);
    error_log("Base upload directory created successfully");
}

// Function to handle file uploads
function uploadFile($file, $subDir, $allowedTypes = []) {
    error_log("Starting file upload process");
    try {
        $targetDir = UPLOAD_BASE_PATH . '/' . $subDir;
        error_log("Upload target directory: " . $targetDir);
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            error_log("Creating directory: " . $targetDir);
            // Create directory with full permissions first
            if (!mkdir($targetDir, 0777, true)) {
                error_log("Failed to create directory with mkdir: " . $targetDir);
                throw new Exception("Failed to create upload directory");
            }
            // Set directory permissions
            if (!chmod($targetDir, 0777)) {
                error_log("Failed to chmod directory: " . $targetDir);
                throw new Exception("Failed to set directory permissions");
            }
            error_log("Directory created successfully: " . $targetDir);
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $targetPath = $targetDir . '/' . $fileName;
        error_log("Target file path: " . $targetPath);

        // Check file type if specified
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                error_log("Invalid file type: " . $mimeType . ". Allowed types: " . implode(', ', $allowedTypes));
                throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedTypes));
            }
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Set file permissions
            if (!chmod($targetPath, 0644)) {
                error_log("Failed to chmod file: " . $targetPath);
                throw new Exception("Failed to set file permissions");
            }
            error_log("File uploaded successfully to: " . $targetPath);
            // Return relative path for database storage
            return $subDir . '/' . $fileName;
        } else {
            $uploadError = error_get_last();
            error_log("Failed to move uploaded file. Upload error code: " . $file['error']);
            error_log("PHP error: " . print_r($uploadError, true));
            throw new Exception("Failed to move uploaded file: " . $file['error']);
        }
    } catch (Exception $e) {
        error_log("File upload error: " . $e->getMessage());
        throw $e;
    }
}

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
$data = [];
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents("php://input"), true);
} else {
    $data = $_POST;
}

$METHOD = $data['method'] ?? '';

$response = ['success' => false];

// For debugging
error_log("Received method: " . $METHOD);
error_log("Received data: " . print_r($data, true));

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

    case 'update-password':
        $response = updatePassword($DB, $data);
        break;

    case 'forgot-password':
        $email = $data->email;
        
        // Check if email exists
        $stmt = $DB->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Bu e-posta adresi ile kayıtlı bir kullanıcı bulunamadı.'
            ]);
            exit;
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Save reset token
        $stmt = $DB->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();
        
        // Return success response with token
        echo json_encode([
            'success' => true,
            'message' => 'Şifre sıfırlama bağlantısı oluşturuldu.',
            'token' => $token
        ]);
        break;

    case 'reset-password':
        $token = $data->token;
        $password = $data->password;
        
        // Verify token and check expiry
        $stmt = $DB->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Geçersiz veya süresi dolmuş sıfırlama bağlantısı.'
            ]);
            exit;
        }
        
        // Hash new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $stmt = $DB->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashedPassword, $token);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Şifreniz başarıyla güncellendi.'
        ]);
        break;

    default:
        $response['error'] = "Invalid method: " . $METHOD;
        $response['success'] = false;
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

        // Store current auto-increment value
        $DB->query("SET @users_auto_increment = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='Therapify' AND TABLE_NAME='users')");

        // Check if email already exists
        $stmt = $DB->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];

        if ($count > 0) {
            $response['error'] = "This email is already in use";
            $DB->rollback();
            // Reset auto-increment value
            $DB->query("ALTER TABLE users AUTO_INCREMENT = @users_auto_increment");
            return $response;
        }

        // Handle profile image upload
        $userImgPath = '';
        if (isset($_FILES['user_img'])) {
            $allowedImageTypes = ['image/jpeg', 'image/png'];
            try {
                $userImgPath = uploadFile($_FILES['user_img'], 'profile_images', $allowedImageTypes);
            } catch (Exception $e) {
                $response['error'] = "Profile image upload failed: " . $e->getMessage();
                $DB->rollback();
                return $response;
            }
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
            $userImgPath,
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
        // Reset auto-increment value
        $DB->query("ALTER TABLE users AUTO_INCREMENT = @users_auto_increment");
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

        // Store current auto-increment values
        $DB->query("SET @users_auto_increment = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='Therapify' AND TABLE_NAME='users')");
        $DB->query("SET @therapist_details_auto_increment = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='Therapify' AND TABLE_NAME='therapist_details')");
        $DB->query("SET @therapist_applications_auto_increment = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='Therapify' AND TABLE_NAME='therapist_applications')");

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

        // Handle file uploads
        $allowedImageTypes = ['image/jpeg', 'image/png'];
        $allowedDocTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        // Upload profile image
        $userImgPath = '';
        if (isset($_FILES['user_img'])) {
            try {
                $userImgPath = uploadFile($_FILES['user_img'], 'profile_images', $allowedImageTypes);
                error_log("Profile image uploaded: " . $userImgPath);
            } catch (Exception $e) {
                error_log("Profile image upload failed: " . $e->getMessage());
                throw new Exception("Profile image upload failed: " . $e->getMessage());
            }
        }

        // Upload CV
        $cvFilePath = '';
        if (isset($_FILES['cv_file'])) {
            try {
                $cvFilePath = uploadFile($_FILES['cv_file'], 'cv', $allowedDocTypes);
                error_log("CV uploaded: " . $cvFilePath);
            } catch (Exception $e) {
                error_log("CV upload failed: " . $e->getMessage());
                throw new Exception("CV upload failed: " . $e->getMessage());
            }
        }

        // Upload diploma
        $diplomaFilePath = '';
        if (isset($_FILES['diploma_file'])) {
            try {
                $diplomaFilePath = uploadFile($_FILES['diploma_file'], 'diploma', array_merge($allowedImageTypes, $allowedDocTypes));
                error_log("Diploma uploaded: " . $diplomaFilePath);
            } catch (Exception $e) {
                error_log("Diploma upload failed: " . $e->getMessage());
                throw new Exception("Diploma upload failed: " . $e->getMessage());
            }
        }

        // Upload license
        $licenseFilePath = '';
        if (isset($_FILES['license_file'])) {
            try {
                $licenseFilePath = uploadFile($_FILES['license_file'], 'license', array_merge($allowedImageTypes, $allowedDocTypes));
                error_log("License uploaded: " . $licenseFilePath);
            } catch (Exception $e) {
                error_log("License upload failed: " . $e->getMessage());
                throw new Exception("License upload failed: " . $e->getMessage());
            }
        }

        // Hash password
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert user with file paths
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
            $userImgPath,
            $hash
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create user account");
        }

        $userId = $stmt->insert_id;

        // Insert therapist details
        $stmt = $DB->prepare("
            INSERT INTO therapist_details (
                user_id, title, about_text, session_fee,
                session_duration, languages_spoken,
                video_session_available, face_to_face_session_available,
                office_address
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issiisiii",
            $userId,
            $data['title'],
            $data['about_text'],
            $data['session_fee'],
            $data['session_duration'],
            $data['languages_spoken'],
            $data['video_session_available'],
            $data['face_to_face_session_available'],
            $data['office_address']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert therapist details");
        }

        // Get the therapist_details id
        $therapistDetailsId = $DB->insert_id;

        // Insert therapist specialties
        $specialties = json_decode($data['specialties'], true);
        
        // First check if specialties exist
        $checkStmt = $DB->prepare("SELECT id FROM specialties WHERE name = ?");
        $insertSpecialtyStmt = $DB->prepare("INSERT INTO specialties (name) VALUES (?)");
        $insertTherapistSpecialtyStmt = $DB->prepare("INSERT INTO therapist_specialties (therapist_id, specialty_id) VALUES (?, ?)");

        foreach ($specialties as $specialty) {
            // Check if specialty exists
            $checkStmt->bind_param("s", $specialty);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows === 0) {
                // Specialty doesn't exist, create it
                $insertSpecialtyStmt->bind_param("s", $specialty);
                if (!$insertSpecialtyStmt->execute()) {
                    throw new Exception("Failed to create specialty: " . $specialty);
                }
                $specialtyId = $DB->insert_id;
            } else {
                $specialtyId = $result->fetch_assoc()['id'];
            }

            // Insert the therapist-specialty relationship
            $insertTherapistSpecialtyStmt->bind_param("ii", $therapistDetailsId, $specialtyId);
            if (!$insertTherapistSpecialtyStmt->execute()) {
                throw new Exception("Failed to insert specialty relationship: " . $specialty);
            }
        }

        // Create therapist application
        $stmt = $DB->prepare("
            INSERT INTO therapist_applications (
                user_id, education, license_number, experience_years,
                cv_file, diploma_file, license_file, application_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->bind_param(
            "ississs",
            $userId,
            $data['education'],
            $data['license_number'],
            $data['experience_years'],
            $cvFilePath,
            $diplomaFilePath,
            $licenseFilePath
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create therapist application");
        }

        // Convert file paths to public URLs for response
        $response['user'] = [
            'id' => $userId,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'user_img' => $userImgPath ? getPublicPath($userImgPath) : '',
            'cv_file' => $cvFilePath ? getPublicPath($cvFilePath) : '',
            'license_file' => $licenseFilePath ? getPublicPath($licenseFilePath) : ''
        ];

        $response['success'] = true;
        $response['message'] = "Therapist registration successful";
        $response['user_id'] = $userId;
        $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        $response['error'] = "Registration failed: " . $e->getMessage();
        error_log("Registration error: " . $e->getMessage());
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

function updatePassword($DB, $data) {
    $response = ['success' => false];

    if (!isset($data['email']) || !isset($data['old_password']) || !isset($data['new_password'])) {
        $response['error'] = 'Eksik bilgi gönderildi.';
        return $response;
    }

    try {
        // Get user by email
        $stmt = $DB->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $response['error'] = 'Kullanıcı bulunamadı.';
            return $response;
        }

        $user = $result->fetch_assoc();

        // Verify old password
        if (!password_verify($data['old_password'], $user['password'])) {
            $response['error'] = 'Mevcut şifre yanlış.';
            return $response;
        }

        // Hash new password
        $newHashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

        // Update password
        $stmt = $DB->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newHashedPassword, $user['id']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Şifreniz başarıyla güncellendi.';
        } else {
            $response['error'] = 'Şifre güncellenirken bir hata oluştu.';
        }

    } catch (Exception $e) {
        $response['error'] = 'Bir hata oluştu: ' . $e->getMessage();
    }

    return $response;
}