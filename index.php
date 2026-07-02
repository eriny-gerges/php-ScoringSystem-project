<?php
require_once __DIR__ . '/head.php';
?>

<div class="container mt-5">
    <h1 class="text-center mb-3">Welcome to the College Competition</h1>
    <p class="text-center text-muted">Join the tournament and follow the live scoreboard.</p>

    <div class="row justify-content-center text-center mt-5 g-4">
        <div class="col-12 col-md-4">
            <a href="join_team.php" class="btn btn-outline-primary btn-lg w-100 py-4 text-uppercase fw-bold">
                Join as Team
            </a>
        </div>
        <div class="col-12 col-md-4">
            <a href="join_individual.php" class="btn btn-outline-primary btn-lg w-100 py-4 text-uppercase fw-bold">
                Join as Individual
            </a>
        </div>
        <div class="col-12 col-md-4">
            <a href="view.php" class="btn btn-outline-success btn-lg w-100 py-4 text-uppercase fw-bold">
                View Scoreboard
            </a>
        </div>
    </div>
</div>

</body>
</html>
