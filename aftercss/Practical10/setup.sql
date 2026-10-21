
USE studenthub_db;

ALTER TABLE users
ADD COLUMN role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
ADD COLUMN last_login DATETIME NULL;

CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector CHAR(32) NOT NULL UNIQUE,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create an admin account role for an existing user.
-- Replace the email with an email already registered in Practical 9.
UPDATE users
SET role = 'admin'
WHERE email = 'admin@example.com';
