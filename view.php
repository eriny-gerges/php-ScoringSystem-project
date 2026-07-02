<?php

require_once __DIR__ . '/ranking.php';

$db = get_db();
$teams       = get_team_rankings($db);
$individuals = get_individual_rankings($db);

require_once __DIR__ . '/head.php';
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Scoreboard</h2>
    <div class="row g-4">
        <div class="col-md-6">
            <?php render_ranking_table('Teams Results', 'bg-primary', $teams, 'team_rank', 'team_name'); ?>
        </div>
        <div class="col-md-6">
            <?php render_ranking_table('Individuals Results', 'bg-success', $individuals, 'individual_rank', 'name'); ?>
        </div>
    </div>
</div>

</body>
</html>
