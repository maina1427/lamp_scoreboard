<?php
// judge/index.php
require_once '../includes/db_connect.php';

$selected_judge_id = null;
$users = [];
$judges = [];
$message = '';

// Fetch all judges for the dropdown
try {
    $judges_stmt = $pdo->query("SELECT id, display_name FROM judges ORDER BY display_name");
    $judges = $judges_stmt->fetchAll();
} catch (\PDOException $e) {
    $message .= '<p style="color: red;">Error fetching judges: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_judge'])) {
    $selected_judge_id = filter_input(INPUT_POST, 'judge_id', FILTER_VALIDATE_INT);
    if ($selected_judge_id === false || $selected_judge_id === null) {
        $message = '<p style="color: red;">Invalid judge selected.</p>';
        $selected_judge_id = null; // Reset if invalid
    } else {
        // Optionally, verify judge_id exists in DB
        $stmt = $pdo->prepare("SELECT id FROM judges WHERE id = ?");
        $stmt->execute([$selected_judge_id]);
        if (!$stmt->fetch()) {
            $message = '<p style="color: red;">Selected judge does not exist.</p>';
            $selected_judge_id = null;
        }
    }
}

// Fetch all users to display, regardless of judge selection initially
try {
    $users_stmt = $pdo->query("SELECT id, username, display_name FROM users ORDER BY display_name");
    $users = $users_stmt->fetchAll();
} catch (\PDOException $e) {
    $message .= '<p style="color: red;">Error fetching users: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Judge Portal</h1>
        <nav>
            <ul>
                <li><a href="../admin/add_judge.php">Admin Panel</a></li>
                <li><a href="index.php">Judge Portal</a></li>
                <li><a href="../public/scoreboard.php">Public Scoreboard</a></li>
            </ul>
        </nav>

        <?php echo $message; ?>

        <?php if ($selected_judge_id === null): ?>
            <form action="index.php" method="POST">
                <label for="judge_id">Select Judge:</label>
                <select id="judge_id" name="judge_id" required>
                    <option value="">-- Select a Judge --</option>
                    <?php foreach ($judges as $judge): ?>
                        <option value="<?php echo htmlspecialchars($judge['id']); ?>">
                            <?php echo htmlspecialchars($judge['display_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="select_judge">Proceed as Judge</button>
            </form>
        <?php else: ?>
            <h2>Scoring for Judge:
                <?php
                    $current_judge_display_name = '';
                    foreach ($judges as $judge) {
                        if ($judge['id'] == $selected_judge_id) {
                            $current_judge_display_name = $judge['display_name'];
                            break;
                        }
                    }
                    echo htmlspecialchars($current_judge_display_name);
                ?>
            </h2>
            <p>Select a user to assign points:</p>

            <?php if (!empty($users)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Display Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['display_name']); ?></td>
                                <td>
                                    <a href="score_user.php?user_id=<?php echo htmlspecialchars($user['id']); ?>&judge_id=<?php echo htmlspecialchars($selected_judge_id); ?>">Assign Points</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users available to score yet. Please add users to the database.</p>
            <?php endif; ?>
            <p><a href="index.php">Go back and select a different judge</a></p>
        <?php endif; ?>
    </div>
</body>
</html>