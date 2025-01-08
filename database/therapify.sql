-- Create Database
CREATE DATABASE IF NOT EXISTS Therapify;

-- Use the database
USE Therapify;

-- Create Users Table (existing)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    address TEXT NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    birth_of_date DATE NOT NULL,
    user_img mediumtext NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_role ENUM('admin', 'user', 'therapist') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Therapist Applications Table
CREATE TABLE IF NOT EXISTS therapist_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    education VARCHAR(255) NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    experience_years INT NOT NULL,
    cv_file VARCHAR(255),
    diploma_file VARCHAR(255),
    license_file VARCHAR(255),
    application_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Therapist Details Table
CREATE TABLE IF NOT EXISTS therapist_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL, -- Dr., Uzm. Psk., vb.
    about_text TEXT NOT NULL,
    session_fee DECIMAL(10,2) NOT NULL,
    session_duration INT NOT NULL DEFAULT 50, -- minutes
    languages_spoken VARCHAR(255), -- JSON array: ["Türkçe", "English"]
    video_session_available BOOLEAN DEFAULT true,
    face_to_face_session_available BOOLEAN DEFAULT true,
    office_address TEXT,
    rating DECIMAL(3,2) DEFAULT 0,
    total_sessions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Therapist Specialties Table
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Therapist-Specialty Relation Table
CREATE TABLE IF NOT EXISTS therapist_specialties (
    therapist_id INT NOT NULL,
    specialty_id INT NOT NULL,
    PRIMARY KEY (therapist_id, specialty_id),
    FOREIGN KEY (therapist_id) REFERENCES therapist_details(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
);


-- Create Therapist Availability Table
CREATE TABLE IF NOT EXISTS therapist_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT NOT NULL,
    day_of_week TINYINT NOT NULL, -- 0=Sunday, 1=Monday, etc.
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (therapist_id) REFERENCES therapist_details(id) ON DELETE CASCADE
);

-- Create indexes for better query performance
CREATE INDEX idx_application_status ON therapist_applications(application_status);
CREATE INDEX idx_therapist_rating ON therapist_details(rating);
CREATE INDEX idx_therapist_languages ON therapist_details(languages_spoken);
CREATE INDEX idx_availability_day ON therapist_availability(day_of_week);

-- Insert some sample specialties
INSERT INTO specialties (name) VALUES 
    ('Klinik Psikoloji'),
    ('Aile Terapisi'),
    ('Çift Terapisi'),
    ('Çocuk ve Ergen'),
    ('Depresyon'),
    ('Anksiyete'),
    ('Travma Sonrası Stres Bozukluğu'),
    ('Obsesif Kompulsif Bozukluk'),
    ('Yeme Bozuklukları'),
    ('Bağımlılık'),
    ('Cinsel Terapi'),
    ('Oyun Terapisi'); 