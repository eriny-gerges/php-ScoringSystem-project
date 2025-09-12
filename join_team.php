<?php require_once "head.php";  
if ($_SERVER["REQUEST_METHOD"] == "GET"): ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form class="border border-primary rounded p-5 shadow" action="join_team.php" method="post">
                <h2 class="text-center text-primary mb-4">Join as a Team</h2>

                <div class="mb-3">
                    <label for="team_name" class="form-label">Team Name</label>
                    <input class="form-control" id="team_name" type="text" name="team_name" required>
                </div>

                <h5 class="text-secondary">Team Members</h5>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="mb-2">
                    <input class="form-control" type="text" name="memb<?= $i ?>" placeholder="Member <?= $i ?>'s name" required>
                </div>
                <?php endfor; ?>

                <hr>
                <h5 class="text-secondary">Team Events</h5>
                <?php
                $events = ["soccer", "volley_ball", "basket_ball", "hand_ball", "cricket"];
                foreach ($events as $event): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="<?= $event ?>" name="<?= $event ?>" value="<?= $event ?>">
                        <label class="form-check-label" for="<?= $event ?>"><?= ucfirst(str_replace("_", " ", $event)) ?></label>
                    </div>
                <?php endforeach; ?>

                <hr>
                <div class="mb-3">
                    <label class="form-label">Total Score</label>
                    <input class="form-control" type="number" name="total_score" required>
                </div>

                <div class="text-center">
                    <button class="btn btn-primary px-5" type="submit">Join</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif ?>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
<?php
    
    $name = trim($_POST["team_name"]);
    $m1 = trim($_POST["memb1"]);
    $m2 = trim($_POST["memb2"]);
    $m3 = trim($_POST["memb3"]);
    $m4 = trim($_POST["memb4"]);
    $m5 = trim($_POST["memb5"]);
    $score = $_POST["total_score"];

    
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        echo "<div class='container text-center mt-5'><h3 class='text-danger'>Team name should only contain letters and spaces.</h3></div>";
        exit();
    }

     
    $members = [$m1, $m2, $m3, $m4, $m5];
    foreach ($members as $member) {
        if (!preg_match("/^[a-zA-Z\s]+$/", $member)) {
            echo "<div class='container text-center mt-5'><h3 class='text-danger'>Member names should only contain letters and spaces.</h3></div>";
            exit();
        }
    }

  
    if (!is_numeric($score) || $score < 0) {
        echo "<div class='container text-center mt-5'><h3 class='text-danger'>Total score should be a valid non-negative number.</h3></div>";
        exit();
    }

  
    require_once "connection.php";
    $conn = connect();
    
    $stmt = $conn->prepare("INSERT INTO teams(team_name, Member_1, Member_2, Member_3, Member_4, Member_5, Total_score) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $name, $m1, $m2, $m3, $m4, $m5, $score);
    $stmt->execute();
    
    $conn->close();
?>
<div class="container text-center mt-5">
    <h2 class="text-success">Team Joined Successfully!</h2>
    <a href="view.php" class="btn btn-outline-primary mt-3">View Scoreboard</a>
</div>
<?php endif ?>
