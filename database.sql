-- Create the database
CREATE DATABASE IF NOT EXISTS students;
USE students;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    service_number VARCHAR(20) NOT NULL UNIQUE,
    rank VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL
);

-- Create the students table
CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY,
    enrollment_date DATE NOT NULL,
    graduation_year INT NOT NULL,
    status ENUM('active', 'inactive', 'graduated') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create the teachers table
CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT PRIMARY KEY,
    department VARCHAR(100) NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create the subjects table
CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(10) NOT NULL UNIQUE,
    subject_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    credits INT NOT NULL
);

-- Create the subject_teachers table
CREATE TABLE IF NOT EXISTS subject_teachers (
    subject_id INT,
    teacher_id INT,
    academic_year VARCHAR(10) NOT NULL,
    semester INT NOT NULL,
    PRIMARY KEY (subject_id, teacher_id, academic_year, semester),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE
);

-- Insert sample data into users table
INSERT INTO users (service_number, rank, name, email, phone_number, password, role) VALUES
('T101', 'Professor', 'John Smith', 'john.smith@school.com', '1234567890', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 'teacher'),
('T102', 'Dr', 'Sarah Johnson', 'sarah.j@school.com', '1234567891', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 'teacher'),
('S101', 'Student', 'Mike Wilson', 'mike.w@school.com', '1234567892', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 'student'),
('S102', 'Student', 'Emma Davis', 'emma.d@school.com', '1234567893', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 'student'),
('S103', 'Student', 'James Brown', 'james.b@school.com', '1234567894', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 'student');

-- Insert sample data into teachers table
INSERT INTO teachers (teacher_id, department) VALUES
((SELECT user_id FROM users WHERE service_number = 'T101'), 'General Studies'),
((SELECT user_id FROM users WHERE service_number = 'T102'), 'General Studies');

-- Insert sample data into students table
INSERT INTO students (student_id, enrollment_date, graduation_year, status) VALUES
((SELECT user_id FROM users WHERE service_number = 'S101'), '2023-09-01', 2027, 'active'),
((SELECT user_id FROM users WHERE service_number = 'S102'), '2023-09-01', 2027, 'active'),
((SELECT user_id FROM users WHERE service_number = 'S103'), '2023-09-01', 2027, 'active');

-- Insert sample data into subjects table
INSERT INTO subjects (subject_code, subject_name, description, credits) VALUES
('MATH101', 'Mathematics', 'Basic mathematics and algebra', 3),
('ENG101', 'English', 'English language and literature', 3),
('CS101', 'Computer Science', 'Introduction to programming', 4),
('PHY101', 'Physics', 'Basic physics concepts', 3);

-- Assign teachers to subjects
INSERT INTO subject_teachers (subject_id, teacher_id, academic_year, semester) VALUES
((SELECT subject_id FROM subjects WHERE subject_code = 'MATH101'), (SELECT user_id FROM users WHERE service_number = 'T101'), '2023-2024', 1),
((SELECT subject_id FROM subjects WHERE subject_code = 'ENG101'), (SELECT user_id FROM users WHERE service_number = 'T102'), '2023-2024', 1),
((SELECT subject_id FROM subjects WHERE subject_code = 'CS101'), (SELECT user_id FROM users WHERE service_number = 'T101'), '2023-2024', 1),
((SELECT subject_id FROM subjects WHERE subject_code = 'PHY101'), (SELECT user_id FROM users WHERE service_number = 'T102'), '2023-2024', 1);