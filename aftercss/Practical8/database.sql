
-- Practical 8: StudentHub Database
CREATE DATABASE IF NOT EXISTS studenthub_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE studenthub_db;

-- Table 1: Students
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL,
    year TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 2: Events
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 3: Registrations
CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Registered', 'Attended', 'Cancelled')
        NOT NULL DEFAULT 'Registered',

    CONSTRAINT fk_registration_student
        FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_registration_event
        FOREIGN KEY (event_id)
        REFERENCES events(event_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT unique_student_event
        UNIQUE (student_id, event_id)
) ENGINE=InnoDB;

-- Sample student data
INSERT INTO students (name, email, course, year) VALUES
('Aarav Patel', 'aarav@example.com', 'BCA', 1),
('Priya Shah', 'priya@example.com', 'B.Tech', 2),
('Rohan Mehta', 'rohan@example.com', 'BCA', 3),
('Neha Desai', 'neha@example.com', 'B.Sc', 1),
('Karan Joshi', 'karan@example.com', 'B.Com', 2);

-- Sample event data
INSERT INTO events (title, description, event_date, location) VALUES
('Web Development Workshop', 'Learn HTML, CSS and PHP basics',
 '2027-01-15 10:00:00', 'Computer Lab 1'),
('Coding Competition', 'A programming competition for students',
 '2027-02-10 09:00:00', 'Main Auditorium'),
('Tech Talk', 'Talk on modern technology trends',
 '2027-02-20 11:00:00', 'Seminar Hall'),
('Project Exhibition', 'Display student software projects',
 '2027-03-05 10:00:00', 'College Hall'),
('Career Guidance Session', 'Career planning and interview skills',
 '2027-03-15 12:00:00', 'Seminar Hall');

-- Sample registration data
INSERT INTO registrations (student_id, event_id, status) VALUES
(1, 1, 'Registered'),
(1, 2, 'Registered'),
(2, 1, 'Attended'),
(3, 3, 'Registered'),
(4, 4, 'Registered'),
(5, 5, 'Registered');
