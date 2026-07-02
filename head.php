<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Competition</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">College Competition</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-white" href="join_team.php">Join Team</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="join_individual.php">Join Individual</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="view.php">Scoreboard</a></li>
                <?php if (is_admin()): ?>
                    <li class="nav-item"><a class="nav-link text-white" href="event_management.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="add_score.php">Add Score</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link text-white" href="login.php">Admin Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php foreach (get_flashes() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
</div>

<script src="assets/js/bootstrap.bundle.min.js" defer></script>
