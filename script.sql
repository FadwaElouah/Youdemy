--Creat la base de donnes

CREATE DATABASE Youdemy;

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    status ENUM('active', 'pending', 'blocked') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des catégories de cours
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des tags
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des cours
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content_type ENUM('video', 'document', 'text') NOT NULL,
    content_url VARCHAR(255),
    teacher_id INT,
    category_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Table relation entre cours et tags (Many-to-Many)
-- CREATE TABLE course_tags (
--     course_id INT,
--     tag_id INT,
--     PRIMARY KEY (course_id, tag_id),
--     FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
--     FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
-- );

-- Table des inscriptions
CREATE TABLE enrollments (
    user_id INT,
    course_id INT,
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    progress INT DEFAULT 0,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    PRIMARY KEY (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Table des statistiques
CREATE TABLE statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    total_students INT DEFAULT 0,
    avg_progress DECIMAL(5,2) DEFAULT 0,
    completion_rate DECIMAL(5,2) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);