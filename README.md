# LAMP Stack Scoreboard Application

This project demonstrates a simple scoring system built entirely on a LAMP (Linux, Apache, MySQL, PHP) stack. It features an admin panel for judge management, a judge portal for scoring participants, and a public-facing dynamic scoreboard.

## Table of Contents

1.  [Features](#features)
2.  [Prerequisites](#prerequisites)
3.  [Setup and Installation](#setup-and-installation)
4.  [Database Schema](#database-schema)
5.  [Assumptions Made](#assumptions-made)
6.  [Design Choices](#design-choices)
7.  [Optional Future Enhancements](#optional-future-enhancements)

## 1. Features

* **Admin Panel:**
    * Add new judges with unique usernames and display names.
    * View a list of existing judges.
* **Judge Portal:**
    * Select a judge from a dropdown (simulates login).
    * View a list of all participating users.
    * Assign points (1-100) to a selected user.
    * Update points for a user if already scored by the same judge.
* **Public Scoreboard:**
    * Displays all users and their total accumulated points.
    * Dynamically updates every 10 seconds using AJAX.
    * Users are highlighted based on their rank (Gold for 1st, Silver for 2nd, Bronze for 3rd, others grey).
    * Sorted in descending order of total points. Handles ties correctly (users with the same total points share the same rank).

## 2. Prerequisites

To run this application, you will need a LAMP environment. This can be set up using:

* **XAMPP / WAMP / MAMP:** Easiest for local development on Windows/macOS.
* **Dockerized LAMP:** For a more isolated and reproducible environment.
* **A traditional LAMP server:** On a Linux machine.

Ensure you have:
* Apache HTTP Server
* MySQL/MariaDB Database
* PHP (version 7.4 or higher recommended, due to PDO usage)
* `php-pdo-mysql` extension enabled for PHP

## 3. Setup and Installation

1.  **Download/Clone the repository:**
    (Assume you have the source code in a directory, e.g., `lamp_scoreboard`)

2.  **Place the project in your web server's document root:**
    * **XAMPP/WAMP:** Copy the `lamp_scoreboard` folder into `htdocs/` (XAMPP) or `www/` (WAMP).
    * **Apache:** Copy `lamp_scoreboard` to `/var/www/html/` (or your configured `DocumentRoot`).

3.  **Configure your Database:**
    * Open your MySQL client (e.g., phpMyAdmin provided by XAMPP/WAMP, MySQL Workbench, or command line).
    * Create a database named `scoreboard_db`.
    * Execute the SQL statements from the `Database Schema` section below to create the tables and populate initial data.

    **Important:** Update the `includes/db_connect.php` file with your database credentials (username and password).
    ```php
    // includes/db_connect.php
    $host = 'localhost';
    $db   = 'scoreboard_db';
    $user = 'root';      // <--- YOUR MYSQL USERNAME
    $pass = '';          // <--- YOUR MYSQL PASSWORD
    ```

4.  **Access the Application:**
    Open your web browser and navigate to:
    * **Admin Panel:** `http://localhost/lamp_scoreboard/admin/add_judge.php`
    * **Judge Portal:** `http://localhost/lamp_scoreboard/judge/index.php`
    * **Public Scoreboard:** `http://localhost/lamp_scoreboard/public/scoreboard.php`

## 4. Database Schema

The following SQL `CREATE TABLE` statements define the database structure:

```sql
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