<?php
// judge/score_user.php
require_once '../includes/db_connect.php';

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$judge_id = filter_input(INPUT_GET, 'judge_id', FILTER_VALIDATE_INT);
$message = '';
$user_display_name = '';
$judge_display_name = '';

if ($user_id === false || $user_id === null || $judge_id === false || $judge_id === null) {
    $message = '<p style="color: red;">Invalid User ID or Judge ID.</p>';
    // Redirect or exit if critical IDs are missing/invalid
    header('Location: index.php');
    exit();
}

// Fetch user's display name
try {
    $stmt = $pdo->prepare("SELECT display_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $user_display_name = $user['display_name'];
    } else {
        $message .= '<p style="color: red;">User not found.</p>';
    }
} catch (\PDOException $e) {
    $message .= '<p style="color: red;">Error fetching user: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Fetch judge's display name
try {
    $stmt = $pdo->prepare("SELECT display_name FROM judges WHERE id = ?");
    $stmt->execute([$judge_id]);
    $judge = $stmt->fetch();
    if ($judge) {
        $judge_display_name = $judge['display_name'];
    } else {
        $message .= '<p style="color: red;">Judge not found.</p>';
    }
} catch (\PDOException $e) {
    $message .= '<p style="color: red;">Error fetching judge: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $points = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);

    if ($points === false || $points < 1 || $points > 100) {
        $message = '<p style="color: red;">Points must be a number between 1 and 100.</p>';
    } else {
        try {
            // Check if this judge has already scored this user
            $stmt = $pdo->prepare("SELECT id FROM scores WHERE user_id = ? AND judge_id = ?");
            $stmt->execute([$user_id, $judge_id]);
            $existing_score = $stmt->fetch();

            if ($existing_score) {
                // Update existing score
                $stmt = $pdo->prepare("UPDATE scores SET points = ?, timestamp = CURRENT_TIMESTAMP WHERE user_id = ? AND judge_id = ?");
                $stmt->execute([$points, $user_id, $judge_id]);
                $message = '<p style="color: green;">Score for ' . htmlspecialchars($user_display_name) . ' updated successfully!</p>';
            } else {
                // Insert new score
                $stmt = $pdo->prepare("INSERT INTO scores (user_id, judge_id, points) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $judge_id, $points]);
                $message = '<p style="color: green;">Score for ' . htmlspecialchars($user_display_name) . ' assigned successfully!</p>';
            }
        } catch (\PDOException $e) {
            $message = '<p style="color: red;">Error assigning score: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}

// Fetch current score for the user by this judge, if any
$current_score = null;
try {
    $stmt = $pdo->prepare("SELECT points FROM scores WHERE user_id = ? AND judge_id = ?");
    $stmt->execute([$user_id, $judge_id]);
    $score_data = $stmt->fetch();
    if ($score_data) {
        $current_score = $score_data['points'];
    }
} catch (\PDOException $e) {
    $message .= '<p style="color: red;">Error fetching current score: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Points</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Assign Points</h1>
        <nav>
            <ul>
                <li><a href="../admin/add_judge.php">Admin Panel</a></li>
                <li><a href="index.php">Judge Portal</a></li>
                <li><a href="../public/scoreboard.php">Public Scoreboard</a></li>
            </ul>
        </nav>

        <?php echo $message; ?>

        <?php if ($user_display_name && $judge_display_name): ?>
            <p>Assigning points to: <strong><?php echo htmlspecialchars($user_display_name); ?></strong></p>
            <p>By Judge: <strong><?php echo htmlspecialchars($judge_display_name); ?></strong></p>
            <?php if ($current_score !== null): ?>
                <p>Current score by this judge: <strong><?php echo htmlspecialchars($current_score); ?></strong></p>
            <?php endif; ?>

            <form action="score_user.php?user_id=<?php echo htmlspecialchars($user_id); ?>&judge_id=<?php echo htmlspecialchars($judge_id); ?>" method="POST">
                <label for="points">Points (1-100):</label><br>
                <input type="number" id="points" name="points" min="1" max="100" value="<?php echo htmlspecialchars($current_score ?? ''); ?>" required><br><br>
                <button type="submit">Assign/Update Points</button>
            </form>
        <?php else: ?>
            <p>Cannot assign points. User or Judge information is missing or invalid.</p>
        <?php endif; ?>

        <p><a href="index.php">Back to Judge Portal</a></p>
    </div>
</body>
</html>