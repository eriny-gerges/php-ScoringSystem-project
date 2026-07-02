<?php
require_once __DIR__ . '/functions.php';
require_admin();

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $type             = $_POST['participant_type'] ?? '';
    $participation_id = (int) ($_POST['participation_id'] ?? 0);
    $points           = $_POST['points'] ?? '';

    if (!in_array($type, ['team', 'individual'], true)) {
        set_flash('Unknown participant type.', 'danger');
        header('Location: add_score.php');
        exit;
    }
    if (!is_valid_points($points)) {
        set_flash('Points must be a whole number of zero or more.', 'danger');
        header('Location: add_score.php');
        exit;
    }

    $points = (int) $points;
    $table  = $type === 'team'
        ? 'team_event_participation'
        : 'individual_event_participation';

    $check = $db->prepare(
        "SELECT e.max_points
           FROM {$table} p
           JOIN events e ON e.event_id = p.event_id
          WHERE p.participation_id = ?"
    );
    $check->bind_param('i', $participation_id);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$row) {
        set_flash('That participation no longer exists.', 'danger');
        header('Location: add_score.php');
        exit;
    }
    if ($points > (int) $row['max_points']) {
        set_flash('Points cannot be greater than the event maximum (' . (int) $row['max_points'] . ').', 'danger');
        header('Location: add_score.php');
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE {$table} SET points = ? WHERE participation_id = ?");
        $stmt->bind_param('ii', $points, $participation_id);
        $stmt->execute();
        $stmt->close();
        set_flash('Score saved. Totals updated automatically.', 'success');
    } catch (mysqli_sql_exception $e) {

        error_log('save score failed: ' . $e->getMessage());
        set_flash('Could not save the score. Check that the points are within the event limit.', 'danger');
    }

    header('Location: add_score.php');
    exit;
}

$team_rows = $db->query(
    'SELECT p.participation_id, t.team_name AS competitor, e.event_name, e.max_points, p.points
       FROM team_event_participation p
       JOIN teams  t ON t.team_id  = p.team_id
       JOIN events e ON e.event_id = p.event_id
      ORDER BY t.team_name, e.event_name'
)->fetch_all(MYSQLI_ASSOC);

$individual_rows = $db->query(
    'SELECT p.participation_id, i.name AS competitor, e.event_name, e.max_points, p.points
       FROM individual_event_participation p
       JOIN individuals i ON i.individual_id = p.individual_id
       JOIN events e ON e.event_id = p.event_id
      ORDER BY i.name, e.event_name'
)->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/head.php';
function render_score_table(string $title, array $rows, string $type): void
{
    ?>
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-primary text-white"><?= e($title) ?></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>Competitor</th><th>Event</th><th>Points</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= e($r['competitor']) ?></td>
                            <td><?= e($r['event_name']) ?> <small class="text-muted">(max <?= (int) $r['max_points'] ?>)</small></td>
                            <td>
                                <form action="add_score.php" method="post" class="d-flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="participant_type" value="<?= e($type) ?>">
                                    <input type="hidden" name="participation_id" value="<?= (int) $r['participation_id'] ?>">
                                    <input type="number" name="points" class="form-control form-control-sm"
                                           style="max-width:90px" min="0" max="<?= (int) $r['max_points'] ?>"
                                           value="<?= (int) $r['points'] ?>" required>
                            </td>
                            <td>
                                    <button class="btn btn-sm btn-success">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr><td colspan="4" class="text-center text-muted">No participations yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>

<div class="container mt-4">
    <h2 class="text-primary mb-4">Add / Update Scores</h2>
    <?php render_score_table('Team Scores', $team_rows, 'team'); ?>
    <?php render_score_table('Individual Scores', $individual_rows, 'individual'); ?>
</div>

</body>
</html>
