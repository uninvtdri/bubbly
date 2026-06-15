CREATE DATABASE IF NOT EXISTS bubbly;
USE bubbly;

CREATE TABLE workout_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exercise_name VARCHAR(100) NOT NULL,
    muscle_group VARCHAR(100),
    sets INT,
    reps INT,
    weight DECIMAL(5,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE workout_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exercise_name VARCHAR(100) NOT NULL,
    sets_done INT,
    reps_done INT,
    weight_used DECIMAL(5,2),
    log_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE body_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weight_kg DECIMAL(5,2) NOT NULL,
    height_cm DECIMAL(5,2) NOT NULL,
    age INT,
    gender VARCHAR(10),
    bmi DECIMAL(5,2),
    bmi_category VARCHAR(30),
    recorded_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
