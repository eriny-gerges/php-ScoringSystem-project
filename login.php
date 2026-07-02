<?php
require_once __DIR__ . '/functions.php';

if (is_admin()) {
    header('Location: add_score.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        set_flash('Please enter both username and password.', 'danger');
        header('Location: login.php');
        exit;
    }

    $db = get_db();
    $stmt = $db->prepare('SELECT admin_id, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);              
        $_SESSION['admin_id'] = (int) $admin['admin_id'];
        set_flash('Welcome back, administrator.', 'success');
        header('Location: add_score.php');
        exit;
    }

    set_flash('Invalid username or password.', 'danger');
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/head.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-primary">
                <div class="card-header bg-primary text-white text-center"><h4 class="mb-0">Administrator Login</h4></div>
                <div class="card-body">
                    <form action="login.php" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
