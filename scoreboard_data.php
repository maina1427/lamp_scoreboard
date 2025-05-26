<?php
// public/scoreboard_data.php
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT
            u.display_name,
            SUM(s.points) AS total_points
        FROM
            users u
        LEFT JOIN
            scores s ON u.id = s.user_id
        GROUP BY
            u.id, u.display_name
        ORDER BY
            total_points DESC, u.display_name ASC
    ");
    $results = $stmt->fetchAll();

    $output = '<table>';
    $output .= '<thead><tr><th>Rank</th><th>User</th><th>Total Points</th></tr></thead>';
    $output .= '<tbody>';

    $rank = 0;
    $prev_total_points = -1; // To handle ties
    $position_counter = 0; // To count unique positions

    foreach ($results as $index => $row) {
        $position_counter++;
        $current_total_points = (int)$row['total_points'];

        if ($current_total_points !== $prev_total_points) {
            $rank = $position_counter;
            $prev_total_points = $current_total_points;
        }

        $highlight_class = '';
        switch ($rank) {
            case 1:
                $highlight_class = 'highlight-gold';
                break;
            case 2:
                $highlight_class = 'highlight-silver';
                break;
            case 3:
                $highlight_class = 'highlight-bronze';
                break;
            default:
                $highlight_class = 'highlight-other';
                break;
        }

        $output .= '<tr class="' . $highlight_class . '">';
        $output .= '<td>' . htmlspecialchars($rank) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['display_name']) . '</td>';
        $output .= '<td>' . htmlspecialchars($current_total_points) . '</td>';
        $output .= '</tr>';
    }

    if (empty($results)) {
        $output .= '<tr><td colspan="3">No scores available yet.</td></tr>';
    }

    $output .= '</tbody>';
    $output .= '</table>';

    echo json_encode(['html' => $output]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>