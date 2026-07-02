<?php

require_once __DIR__ . '/functions.php';

$db = get_db();
$team_events = get_events_by_type($db, 'team');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $team_name = trim($_POST['team_name'] ?? '');
    $members   = $_POST['members'] ?? [];
    $events    = $_POST['events']  ?? [];
    $errors = [];

    if (!is_valid_name($team_name)) {
        $errors[] = 'Team name must contain letters and spaces only.';
    }

    if (!is_array($members) || count($members) !== 5) {
        $errors[] = 'Please enter exactly five team members.';
    } else {
        foreach ($members as $m) {
            if (!is_valid_name((string) $m)) {
                $errors[] = 'Member names must contain letters and spaces only.';
                break;
            }
        }
    }


    $valid_event_ids = array_column($team_events, 'event_id');
    $events = array_values(array_unique(array_map('intval', (array) $events)));
    if (count($events) < 1 || count($events) > 5) {
        $errors[] = 'A team must join between 1 and 5 events.';
    }
    foreach ($events as $eid) {
        if (!in_array($eid, array_map('intval', $valid_event_ids), true)) {
            $errors[] = 'One of the selected events is not valid.';
            break;
        }
    }

    if ($errors) {
        foreach ($errors as $msg) {
            set_flash($msg, 'danger');
        }
        header('Location: join_team.php');
        exit;
    }

    try {
        $db->begin_transaction();

        $stmt = $db->prepare('INSERT INTO teams (team_name) VALUES (?)');
        $stmt->bind_param('s', $team_name);
        $stmt->execute();
        $team_id = $db->insert_id;
        $stmt->close();

        $stmt = $db->prepare('INSERT INTO team_members (team_id, member_name) VALUES (?, ?)');
        foreach ($members as $m) {
            $name = trim((string) $m);
            $stmt->bind_param('is', $team_id, $name);
            $stmt->execute();
        }
        $stmt->close();

        $stmt = $db->prepare('INSERT INTO team_event_participation (team_id, event_id, points) VALUES (?, ?, 0)');
        foreach ($events as $eid) {
            $stmt->bind_param('ii', $team_id, $eid);
            $stmt->execute();
        }
        $stmt->close();

        $db->commit();
        set_flash('Team registered successfully!', 'success');
        header('Location: view.php');
        exit;

    } catch (mysqli_sql_exception $e) {
        $db->rollback();
        error_log('join_team failed: ' . $e->getMessage());
        set_flash('Could not register the team. The team name may already be taken.', 'danger');
        header('Location: join_team.php');
        exit;
    }
}

require_once __DIR__ . '/head.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form class="border border-primary rounded p-4 shadow bg-white" action="join_team.php" method="post">
                <h2 class="text-center text-primary mb-4">Join as a Team</h2>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Team Name</label>
                    <input class="form-control" type="text" name="team_name" maxlength="100" required>
                </div>

                <h5 class="text-secondary">Team Members</h5>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="mb-2">
                        <input class="form-control" type="text" name="members[]" maxlength="100"
                               placeholder="Member <?= $i ?>'s name" required>
                    </div>
                <?php endfor; ?>

                <hr>
                <h5 class="text-secondary">Team Events <small class="text-muted">(choose 1 to 5)</small></h5>
                <?php if (!$team_events): ?>
                    <p class="text-muted">No team events have been created yet.</p>
                <?php else: ?>
                    <?php foreach ($team_events as $ev): ?>
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
                    <button class="btn btn-primary px-5" type="submit">Join</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
