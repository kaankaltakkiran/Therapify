-- Create Database
CREATE DATABASE IF NOT EXISTS Therapify;

-- Use the database
USE Therapify;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    phone_number VARCHAR(20),
    birth_of_date DATE,
    user_img VARCHAR(255),
    user_role ENUM('user', 'therapist', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Therapist Applications Table
CREATE TABLE IF NOT EXISTS therapist_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    education TEXT NOT NULL,
    license_number VARCHAR(255) NOT NULL,
    experience_years INT NOT NULL,
    cv_file VARCHAR(255) NOT NULL,
    diploma_file VARCHAR(255) NOT NULL,
    license_file VARCHAR(255) NOT NULL,
    application_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Therapist Details Table
CREATE TABLE IF NOT EXISTS therapist_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    about_text TEXT NOT NULL,
    session_fee DECIMAL(10,2) NOT NULL,
    session_duration INT NOT NULL,
    languages_spoken JSON NOT NULL,
    video_session_available BOOLEAN DEFAULT TRUE,
    face_to_face_session_available BOOLEAN DEFAULT TRUE,
    office_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Specialties Table
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Therapist-Specialty Relation Table
CREATE TABLE IF NOT EXISTS therapist_specialties (
    therapist_id INT NOT NULL,
    specialty_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (therapist_id, specialty_id),
    FOREIGN KEY (therapist_id) REFERENCES therapist_details(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
);

-- Insert Initial Data into Specialties
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
