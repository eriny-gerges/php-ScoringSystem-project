<?php require_once "head.php"; ?>
<?php if ($_SERVER["REQUEST_METHOD"] == "GET"): ?>   

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Join as Individual</h3>
                </div>
                <div class="card-body">
                    <form action="join_individual.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="ind_name" class="form-control" required>
                        </div>
                        
                        <hr>
                        <h5 class="text-primary">Events You Share In</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="chees" value="chees" id="chees">
                            <label class="form-check-label" for="chees">Chess</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="running" value="running" id="running" required>
                            <label class="form-check-label" for="running">Running</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="table_tennis" value="table_tennis" id="table_tennis">
                            <label class="form-check-label" for="table_tennis">Table Tennis</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tennis" value="tennis" id="tennis">
                            <label class="form-check-label" for="tennis">Tennis</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="swimming" value="swimming" id="swimming">
                            <label class="form-check-label" for="swimming">Swimming</label>
                        </div>
                        
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Total Score</label>
                            <input type="number" name="total_score" class="form-control" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-outline-primary">Join</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <?php
         
        $name = trim($_POST["ind_name"]);
        $score = $_POST["total_score"];

        
        if (empty($name) || !preg_match("/^[a-zA-Z ]*$/", $name)) {
            echo "<div class='container mt-5 text-center'><div class='alert alert-danger'>Invalid name. Please enter a valid name containing only letters and spaces.</div></div>";
            exit;
        }

        
        if (!is_numeric($score) || $score < 0) {
            echo "<div class='container mt-5 text-center'><div class='alert alert-danger'>Invalid score. Please enter a valid score.</div></div>";
            exit;
        }

        require_once "connection.php";
        $conn = connect();
        $conn->query("INSERT INTO individual_participation(name, Total_score) 
                      VALUES ('$name', '$score')");

        $conn->close();
    ?>
    <div class="container mt-5 text-center">
        <div class="alert alert-success fw-bold">
              Joined Successfully!
        </div>
        <a href="view.php" class="btn btn-primary">View Score Rank</a>
    </div>
<?php endif; ?>

