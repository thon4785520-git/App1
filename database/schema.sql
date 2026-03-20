CREATE DATABASE IF NOT EXISTS expert_directory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE expert_directory;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'expert', 'viewer') NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE experts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    position_title VARCHAR(150) NULL,
    department VARCHAR(150) NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(120) NOT NULL,
    profile_image VARCHAR(255) NULL,
    resume_file VARCHAR(255) NULL,
    expertise_summary TEXT NULL,
    portfolio_url VARCHAR(255) NULL,
    approval_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_experts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE work_experience (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT UNSIGNED NOT NULL,
    organization VARCHAR(180) NOT NULL,
    project_name VARCHAR(180) NULL,
    role_title VARCHAR(150) NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    description TEXT NULL,
    CONSTRAINT fk_work_experience_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

CREATE TABLE research (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT UNSIGNED NOT NULL,
    category ENUM('research', 'article', 'patent', 'publication') NOT NULL,
    title VARCHAR(255) NOT NULL,
    publication_name VARCHAR(255) NULL,
    published_year YEAR NULL,
    description TEXT NULL,
    link_url VARCHAR(255) NULL,
    CONSTRAINT fk_research_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

CREATE TABLE training (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT UNSIGNED NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    provider_name VARCHAR(255) NULL,
    certificate_name VARCHAR(255) NULL,
    certificate_file VARCHAR(255) NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    description TEXT NULL,
    CONSTRAINT fk_training_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

CREATE TABLE seminars (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT UNSIGNED NOT NULL,
    seminar_name VARCHAR(255) NOT NULL,
    organizer_name VARCHAR(255) NULL,
    joined_date DATE NULL,
    description TEXT NULL,
    CONSTRAINT fk_seminars_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

CREATE TABLE awards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    issuer_name VARCHAR(255) NULL,
    award_year YEAR NULL,
    description TEXT NULL,
    CONSTRAINT fk_awards_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

CREATE TABLE skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE expert_skill (
    expert_id BIGINT UNSIGNED NOT NULL,
    skill_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (expert_id, skill_id),
    CONSTRAINT fk_expert_skill_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE,
    CONSTRAINT fk_expert_skill_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

CREATE TABLE social_links (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT UNSIGNED NOT NULL,
    platform_name VARCHAR(80) NOT NULL,
    link_url VARCHAR(255) NOT NULL,
    CONSTRAINT fk_social_links_expert FOREIGN KEY (expert_id) REFERENCES experts(id) ON DELETE CASCADE
);

INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Administrator', 'admin@skru.ac.th', '$2y$10$y2.dT3GNHq8ud7g0kJczYeMNHQ79gXX5jU0vbJIUKGwEuITdSb9lK', 'admin');
