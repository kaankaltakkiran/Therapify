# Therapify API Documentation

This folder contains the Postman collection for testing and interacting with the Therapify API endpoints.

## Getting Started

1. Install [Postman](https://www.postman.com/downloads/)
2. Import the collection file `Therapify.postman_collection.json` into Postman
3. Set up your environment variables (if needed)

## Available Endpoints

### Authentication

#### 1. Login
- **Method**: POST
- **Endpoint**: `/api/auth.php`
- **Body**: JSON
  ```json
  {
    "method": "login",
    "email": "your-email@example.com",
    "password": "your-password"
  }
  ```
- **Response**: Returns JWT token and user details

#### 2. Register User
- **Method**: POST
- **Endpoint**: `/api/auth.php`
- **Body**: JSON
  ```json
  {
    "method": "register",
    "first_name": "John",
    "last_name": "Doe",
    "email": "user@example.com",
    "password": "password123",
    "user_role": "user"
  }
  ```
- **Response**: Success message and user ID

#### 3. Register Therapist
- **Method**: POST
- **Endpoint**: `/api/auth.php`
- **Body**: Form-data
  - method: "therapist-register"
  - (all user fields)
  - title
  - about_text
  - session_fee
  - session_duration
  - languages_spoken (JSON array)
  - video_session_available
  - face_to_face_session_available
  - office_address
  - education
  - license_number
  - experience_years
  - specialties (JSON array)
  - cv_file (file)
  - diploma_file (file)
  - license_file (file)
- **Response**: Success message and user ID

### Admin

#### 1. Get Therapist Applications
- **Method**: POST
- **Endpoint**: `/api/admin.php`
- **Header**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  ```
- **Body**: JSON
  ```json
  {
    "method": "get-therapist-applications"
  }
  ```
- **Response**: List of all therapist applications

#### 2. Update Application Status
- **Method**: POST
- **Endpoint**: `/api/admin.php`
- **Header**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  ```
- **Body**: JSON
  ```json
  {
    "method": "update-application-status",
    "application_id": 1,
    "status": "approved"
  }
  ```
- **Response**: Success message

#### 3. Submit Contact
- **Method**: POST
- **Endpoint**: `/api/admin.php`
- **Body**: JSON
  ```json
  {
    "method": "submit-contact",
    "name": "John Doe",
    "email": "john@example.com",
    "message": "Your message here"
  }
  ```
- **Response**: Success message

#### 4. Get Contact Messages
- **Method**: POST
- **Endpoint**: `/api/admin.php`
- **Header**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  ```
- **Body**: JSON
  ```json
  {
    "method": "get-contact-messages"
  }
  ```
- **Response**: List of all contact messages

#### 5. Update Message Status
- **Method**: POST
- **Endpoint**: `/api/admin.php`
- **Header**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  ```
- **Body**: JSON
  ```json
  {
    "method": "update-contact-status",
    "messageId": 1,
    "status": "read"
  }
  ```
- **Response**: Success message

#### 6. Get Unread Messages Count
- **Method**: POST
- **Endpoint**: `/api/admin.php`
- **Header**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  ```
- **Body**: JSON
  ```json
  {
    "method": "get-unread-messages-count"
  }
  ```
- **Response**: Count of unread messages

### Therapist

#### 1. Get Approved Therapists
- **Method**: POST
- **Endpoint**: `/api/therapist.php`
- **Body**: JSON
  ```json
  {
    "method": "get-approved-therapists"
  }
  ```
- **Response**: List of approved therapists with their details

## Testing Tips

1. Start with user registration or login to get a JWT token
2. For protected endpoints, add the token in the Authorization header:
   ```
   Authorization: Bearer your-jwt-token
   ```
3. When testing file uploads, ensure the files exist in your system
4. Check response status codes and messages for debugging

## Common Issues

1. **File Upload Fails**: Ensure file size is within limits and file type is allowed
2. **Authentication Fails**: Verify email and password are correct
3. **JWT Token Invalid**: Token might be expired, try logging in again
4. **CORS Issues**: Check if your API server allows requests from your domain 