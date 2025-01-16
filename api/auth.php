<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

// JWT constants
define('JWT_SECRET_KEY', 'vypLCUJ3Q8oFj7'); // Use a secure secret key in production
define('JWT_EXPIRE_TIME', 3600); // Token validity period (in seconds, 1 hour)

// Load environment variables
$env = parse_ini_file(__DIR__ . '/.env');

// Constants for file uploads
define('UPLOAD_BASE_PATH', '/var/www/my_webapp__2/www/uploads');
define('UPLOAD_BASE_URL', $env['UPLOAD_BASE_URL'] ?? '/uploads'); // This will be the public URL path

// Function to get public URL for uploaded files
function getPublicPath($serverPath) {
    // If the path is empty, return empty string
    if (empty($serverPath)) {
        return '';
    }

    // Get environment variables
    $env = parse_ini_file(__DIR__ . '/.env');
    $appEnv = $env['APP_ENV'] ?? 'development';

    // If in production, return full URL
    if ($appEnv === 'production') {
        $filename = basename($serverPath);
        $type = '';
        
        // Determine file type from path
        if (strpos($serverPath, 'cv') !== false) {
            $type = 'cv';
        } elseif (strpos($serverPath, 'diploma') !== false) {
            $type = 'diploma';
        } elseif (strpos($serverPath, 'license') !== false) {
            $type = 'license';
        } elseif (strpos($serverPath, 'profile_images') !== false || strpos($serverPath, 'user_img') !== false) {
            $type = 'profile_images';
        }

        return $env['UPLOAD_BASE_URL'] . '/' . $type . '/' . $filename;
    }

    // For development, use local path
    return str_replace(UPLOAD_BASE_PATH, UPLOAD_BASE_URL, $serverPath);
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
        'user_role' => $user['user_role'],
        'user_img' => $user['user_img'] ?? null
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

// File upload handling functions
function uploadFile($file, $subDir, $allowedTypes = []) {
    try {
        $targetDir = UPLOAD_BASE_PATH . '/' . $subDir;
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $targetPath = $targetDir . '/' . $fileName;

        // Check file type if specified
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedTypes));
            }
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return getPublicPath($targetPath); // Return the public URL path
        } else {
            throw new Exception("Failed to move uploaded file");
        }
    } catch (Exception $e) {
        error_log("File upload error: " . $e->getMessage());
        throw $e;
    }
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
            // Reset auto-increment values
            $DB->query("ALTER TABLE users AUTO_INCREMENT = @users_auto_increment");
            $DB->query("ALTER TABLE therapist_details AUTO_INCREMENT = @therapist_details_auto_increment");
            $DB->query("ALTER TABLE therapist_applications AUTO_INCREMENT = @therapist_applications_auto_increment");
            return $response;
        }

        // Handle file uploads
        $allowedImageTypes = ['image/jpeg', 'image/png'];
        $allowedDocTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        // Upload profile image
        $userImgPath = '';
        if (isset($_FILES['user_img'])) {
            $userImgPath = uploadFile($_FILES['user_img'], 'profile_images', $allowedImageTypes);
        }

        // Upload CV
        $cvFilePath = '';
        if (isset($_FILES['cv_file'])) {
            $cvFilePath = uploadFile($_FILES['cv_file'], 'cv', $allowedDocTypes);
        }

        // Upload diploma
        $diplomaFilePath = '';
        if (isset($_FILES['diploma_file'])) {
            $diplomaFilePath = uploadFile($_FILES['diploma_file'], 'diploma', array_merge($allowedImageTypes, $allowedDocTypes));
        }

        // Upload license
        $licenseFilePath = '';
        if (isset($_FILES['license_file'])) {
            $licenseFilePath = uploadFile($_FILES['license_file'], 'license', array_merge($allowedImageTypes, $allowedDocTypes));
        }

        // Hash password
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert user
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

        // Send welcome email with temporary password
        // TODO: Implement email sending functionality
        // mail($data['email'], "Welcome to Therapify", "Your temporary password is: " . $tempPassword);

        $response['success'] = true;
        $response['message'] = "Therapist registration successful. Please check your email for login credentials.";
        $response['user_id'] = $userId;
        $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        // Reset auto-increment values
        $DB->query("ALTER TABLE users AUTO_INCREMENT = @users_auto_increment");
        $DB->query("ALTER TABLE therapist_details AUTO_INCREMENT = @therapist_details_auto_increment");
        $DB->query("ALTER TABLE therapist_applications AUTO_INCREMENT = @therapist_applications_auto_increment");
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

                // Handle user image path
                if (!empty($user['user_img'])) {
                    // If path doesn't start with http or https, prepend the base URL
                    if (!preg_match('/^https?:\/\//', $user['user_img'])) {
                        // Remove any leading slashes
                        $user['user_img'] = ltrim($user['user_img'], '/');
                        // If we're in production
                        if (getenv('APP_ENV') === 'production') {
                            $user['user_img'] = 'https://therapify-api.kaankaltakkiran.com/uploads/' . $user['user_img'];
                        } else {
                            $user['user_img'] = 'http://localhost/uploads/' . $user['user_img'];
                        }
                    }
                }

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
        error_log("Login error: " . $e->getMessage());
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
