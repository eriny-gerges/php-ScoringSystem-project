<?php

require_once __DIR__ . '/functions.php';

$db = get_db();
$individual_events = get_events_by_type($db, 'individual');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name   = trim($_POST['ind_name'] ?? '');
    $events = $_POST['events'] ?? [];

    $errors = [];

    if (!is_valid_name($name)) {
        $errors[] = 'Name must contain letters and spaces only.';
    }

    $valid_event_ids = array_map('intval', array_column($individual_events, 'event_id'));
    $events = array_values(array_unique(array_map('intval', (array) $events)));
    if (count($events) < 1 || count($events) > 5) {
        $errors[] = 'You must join between 1 and 5 events.';
    }
    foreach ($events as $eid) {
        if (!in_array($eid, $valid_event_ids, true)) {
            $errors[] = 'One of the selected events is not valid.';
            break;
        }
    }

    if ($errors) {
        foreach ($errors as $msg) {
            set_flash($msg, 'danger');
        }
        header('Location: join_individual.php');
        exit;
    }

    try {
        $db->begin_transaction();

        $stmt = $db->prepare('INSERT INTO individuals (name) VALUES (?)');
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $individual_id = $db->insert_id;
        $stmt->close();

        $stmt = $db->prepare('INSERT INTO individual_event_participation (individual_id, event_id, points) VALUES (?, ?, 0)');
        foreach ($events as $eid) {
            $stmt->bind_param('ii', $individual_id, $eid);
            $stmt->execute();
        }
        $stmt->close();

        $db->commit();
        set_flash('You have joined successfully!', 'success');
        header('Location: view.php');
        exit;

    } catch (mysqli_sql_exception $e) {
        $db->rollback();
        error_log('join_individual failed: ' . $e->getMessage());
        set_flash('Could not register. The name may already be taken.', 'danger');
        header('Location: join_individual.php');
        exit;
    }
}

require_once __DIR__ . '/head.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-primary">
                <div class="card-header bg-primary text-white text-center"><h3>Join as Individual</h3></div>
                <div class="card-body">
                    <form action="join_individual.php" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="ind_name" class="form-control" maxlength="100" required>
                        </div>

                        <hr>
                        <h5 class="text-primary">Events You Join <small class="text-muted">(choose 1 to 5)</small></h5>
                        <?php if (!$individual_events): ?>
                            <p class="text-muted">No individual events have been created yet.</p>
                        <?php else: ?>
                            <?php foreach ($individual_events as $ev): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="events[]"
                                           value="<?= (int) $ev['event_id'] ?>" id="ev<?= (int) $ev['event_id'] ?>">
                                    <label class="form-check-label" for="ev<?= (int) $ev['event_id'] ?>">
                                        <?= e($ev['event_name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5">Join</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
