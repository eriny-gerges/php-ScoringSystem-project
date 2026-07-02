<?php
require_once __DIR__ . '/functions.php';
require_admin();

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['event_name'] ?? '');
        $type = $_POST['event_type'] ?? '';
        $max  = $_POST['max_points'] ?? '';

        $errors = [];
        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Event name is required (max 100 characters).';
        }
        if (!in_array($type, ['team', 'individual'], true)) {
            $errors[] = 'Event type must be team or individual.';
        }
        if (!is_valid_points($max) || (int) $max < 1) {
            $errors[] = 'Max points must be a whole number of 1 or more.';
        }

        if ($errors) {
            foreach ($errors as $m) {
                set_flash($m, 'danger');
            }
        } else {
            try {
                $max = (int) $max;
                $stmt = $db->prepare('INSERT INTO events (event_name, event_type, max_points) VALUES (?, ?, ?)');
                $stmt->bind_param('ssi', $name, $type, $max);
                $stmt->execute();
                $stmt->close();
                set_flash('Event added successfully.', 'success');
            } catch (mysqli_sql_exception $e) {
                error_log('add event failed: ' . $e->getMessage());
                set_flash('Could not add the event. It may already exist.', 'danger');
            }
        }
    }

    if ($action === 'delete') {
        $event_id = (int) ($_POST['event_id'] ?? 0);
        try {
            $stmt = $db->prepare('DELETE FROM events WHERE event_id = ?');
            $stmt->bind_param('i', $event_id);
            $stmt->execute();
            $stmt->close();
            set_flash('Event deleted.', 'info');
        } catch (mysqli_sql_exception $e) {
            error_log('delete event failed: ' . $e->getMessage());
            set_flash('Could not delete the event.', 'danger');
        }
    }

    header('Location: event_management.php');
    exit;
}

$events = $db->query(
    'SELECT event_id, event_name, event_type, max_points FROM events ORDER BY event_type, event_name'
)->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/head.php';
?>

<div class="container mt-4">
    <h2 class="text-primary mb-4">Event Management</h2>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">Add New Event</div>
                <div class="card-body">
                    <form action="event_management.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Event Name</label>
                            <input type="text" name="event_name" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="event_type" class="form-select" required>
                                <option value="team">Team</option>
                                <option value="individual">Individual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Points</label>
                            <input type="number" name="max_points" class="form-control" min="1" value="10" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Event</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-secondary text-white">Existing Events</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Type</th><th>Max</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $ev): ?>
                                <tr>
                                    <td><?= e($ev['event_name']) ?></td>
                                    <td><?= e($ev['event_type']) ?></td>
                                    <td><?= (int) $ev['max_points'] ?></td>
                                    <td class="text-end">
                                        <form action="event_management.php" method="post"
                                              onsubmit="return confirm('Delete this event and its scores?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="event_id" value="<?= (int) $ev['event_id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$events): ?>
                                <tr><td colspan="4" class="text-center text-muted">No events yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
