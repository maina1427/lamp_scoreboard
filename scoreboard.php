<?php
// public/scoreboard.php
require_once '../includes/db_connect.php';

// This page will primarily load the HTML structure.
// The scores will be fetched dynamically via AJAX by scoreboard_data.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Scoreboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Specific styles for scoreboard highlighting */
        .highlight-gold { background-color: #ffd700; } /* Gold for 1st */
        .highlight-silver { background-color: #c0c0c0; } /* Silver for 2nd */
        .highlight-bronze { background-color: #cd7f32; } /* Bronze for 3rd */
        .highlight-other { background-color: #e0e0e0; } /* Light grey for others */
    </style>
</head>
<body>
    <div class="container">
        <h1>Public Scoreboard</h1>
        <nav>
            <ul>
                <li><a href="../admin/add_judge.php">Admin Panel</a></li>
                <li><a href="../judge/index.php">Judge Portal</a></li>
                <li><a href="scoreboard.php">Public Scoreboard</a></li>
            </ul>
        </nav>

        <div id="scoreboard-data">
            Loading scoreboard...
        </div>
    </div>

    <script src="../js/scoreboard_refresh.js"></script>
</body>
</html>