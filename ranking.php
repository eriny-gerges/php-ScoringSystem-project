<?php
require_once __DIR__ . '/functions.php';

function get_team_rankings(mysqli $db): array
{
    return $db->query(
        'SELECT team_rank, team_name, total_score, best_event, events_played
           FROM team_rankings
          ORDER BY team_rank, best_event DESC, team_name'
    )->fetch_all(MYSQLI_ASSOC);
}

function get_individual_rankings(mysqli $db): array
{
    return $db->query(
        'SELECT individual_rank, name, total_score, best_event, events_played
           FROM individual_rankings
          ORDER BY individual_rank, best_event DESC, name'
    )->fetch_all(MYSQLI_ASSOC);
}


function render_ranking_table(string $title, string $header_color, array $rows, string $rank_key, string $name_key): void
{
    ?>
    <div class="card shadow border-0">
        <div class="card-header <?= e($header_color) ?> text-white text-center">
            <h4 class="mb-0"><?= e($title) ?></h4>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr><th>Rank</th><th>Name</th><th>Events</th><th>Total Score</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= (int) $row[$rank_key] ?></td>
                            <td><?= e($row[$name_key]) ?></td>
                            <td><?= (int) $row['events_played'] ?></td>
                            <td><?= (int) $row['total_score'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr><td colspan="4" class="text-center text-muted">No results yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
