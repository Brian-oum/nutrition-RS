--- all database code for the project i.e tables and records
CREATE DATABASE nutrition_system;

USE nutrition_system;

CREATE TABLE caregiver (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
