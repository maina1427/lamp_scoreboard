<?php
// admin/add_judge.php
require_once '../includes/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $display_name = trim($_POST['display_name']);

    if (empty($username) || empty($display_name)) {
        $message = '<p style="color: red;">Username and Display Name cannot be empty.</p>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (?, ?)");
            $stmt->execute([$username, $display_name]);
            $message = '<p style="color: green;">Judge "' . htmlspecialchars($display_name) . '" added successfully!</p>';
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error code
                $message = '<p style="color: red;">Error: Judge with that username already exists.</p>';
            } else {
                $message = '<p style="color: red;">Error adding judge: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        }
    }
}

// Fetch all judges to display
try {
    $judges_stmt = $pdo->query("SELECT * FROM judges ORDER BY display_name");
    $judges = $judges_stmt->fetchAll();
} catch (\PDOException $e) {
    $judges = [];
    $message .= '<p style="color: red;">Error fetching judges: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add Judge</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Add New Judge</h1>
        <nav>
            <ul>
                <li><a href="add_judge.php">Add Judge</a></li>
                <li><a href="../judge/index.php">Judge Portal</a></li>
                <li><a href="../public/scoreboard.php">Public Scoreboard</a></li>
            </ul>
        </nav>

        <?php echo $message; ?>

        <form action="add_judge.php" method="POST">
            <label for="username">Judge Username (Unique ID):</label><br>
            <input type="text" id="username" name="username" required><br><br>

            <label for="display_name">Display Name:</label><br>
            <input type="text" id="display_name" name="display_name" required><br><br>

            <button type="submit">Add Judge</button>
        </form>

        <h2>Existing Judges</h2>
        <?php if (!empty($judges)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Display Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($judges as $judge): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($judge['id']); ?></td>
                            <td><?php echo htmlspecialchars($judge['username']); ?></td>
                            <td><?php echo htmlspecialchars($judge['display_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No judges added yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>