CREATE DATABASE IF NOT EXISTS scoreboard_db;

USE scoreboard_db;

-- Judges Table
CREATE TABLE IF NOT EXISTS judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

-- Users (Participants) Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

-- Scores Table
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judge_id INT NOT NULL,
    points INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE
);

-- Initial Data (Optional, but useful for testing)

INSERT INTO users (username, display_name) VALUES
('alice_p', 'Alice Participant'),
('bob_s', 'Bob the Speaker'),
('charlie_d', 'Charlie Developer');

INSERT INTO judges (username, display_name) VALUES
('judge_1', 'Judge One'),
('judge_2', 'Judge Two');