<?php
session_start();


if(isset($_SESSION['user_id']) && isset($_SESSION['role'])){
    switch($_SESSION['role']){
        case 'student':
            header("Location: student/student_dashboard.php");
            exit();
        case 'faculty':
            header("Location: faculty/faculty_dashboard.php");
            exit();
        case 'admin':
            header("Location: admin/admin_dashboard.php");
            exit();
    }
}

include __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Enrollment System</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: #f4f6f9;
}
.login-card{
    max-width: 400px;
    margin: 80px auto;
}
</style>
</head>
<body>

<div class="container">
    <div class="card login-card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Enrollment System</h4>
        </div>
        <div class="card-body">

            <?php if(isset($login_error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($login_error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary">
                        Login
                    </button>
                </div>
            </form>

        </div>
        <div class="card-footer text-center">
            <small>
                Don’t have an account?
                <a href="register.php">Create one</a>
            </small>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
