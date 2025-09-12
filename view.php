<?php
require_once "connection.php";
require_once "head.php";
$conn = connect();
$team_qy = $conn->query("SELECT team_name, Total_score FROM teams ORDER BY Total_score DESC");
$indi_qy = $conn->query("SELECT name, Total_score FROM individual_participation ORDER BY Total_score DESC");
$conn->close();
?>

<div class="container mt-5">
    <div class="row g-4">

        
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"> Teams Results</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Rank</th>
                                <th scope="col">Team Name</th>
                                <th scope="col">Total Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while ($team = $team_qy->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($team["team_name"]) ?></td>
                                    <td><?= $team["Total_score"] ?></td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"> Individuals Results</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Rank</th>
                                <th scope="col">Name</th>
                                <th scope="col">Total Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while ($indi = $indi_qy->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($indi["name"]) ?></td>
                                    <td><?= $indi["Total_score"] ?></td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

 