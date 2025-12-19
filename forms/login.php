<?php
session_start();
require '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmtLog = $pdo->prepare("INSERT INTO activity_logs (user_id, action_performed, ip_address) VALUES (?, 'User logged into the system', ?)");
        $stmtLog->execute([$user['id'], $ip]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['profile_pic'] = $user['profile_pic'];

        switch ($user['role']) {
            case 'student':
                header('Location: ../student/student.php');
                break;
            case 'reviewer':
                header('Location: ../reviewer/notifications.php');
                break;
            case 'admin':
                header('Location: ../admin/manage_users.php');
                break;
            default:
                header('Location: ../index.php');
                break;
        }
        exit;
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #4e73df; --secondary-color: #224abe; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { border: none; border-radius: 1.25rem; background: rgba(255, 255, 255, 0.95); box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; width: 100%; max-width: 400px; }
        .card-header { background: #ffffff; border-bottom: 1px solid #f1f1f1; padding: 2.5rem 1rem 1rem; text-align: center; }
        .card-header h4 { font-weight: 600; color: #333; margin-bottom: 5px; }
        .form-label { font-size: 0.8rem; font-weight: 600; color: #4e73df; text-transform: uppercase; margin-bottom: 0.5rem; }
        .form-control { padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #d1d3e2; font-size: 0.9rem; }
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1); }
        .btn-primary { background: var(--primary-color); border: none; padding: 0.8rem; font-weight: 600; border-radius: 0.5rem; margin-top: 0.5rem; transition: all 0.3s ease; }
        .btn-primary:hover { background: var(--secondary-color); transform: translateY(-1px); }
        .register-link { color: var(--primary-color); text-decoration: none; font-weight: 600; }
        .register-link:hover { text-decoration: underline; }
        .alert { animation: fadeIn 0.4s ease; border: none; font-size: 0.85rem; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="card shadow">
    <div class="card-header text-center">
        <h4>Welcome Back</h4>
        <p class="text-muted small">Thesis Archives Management System</p>
    </div>
    
    <div class="card-body p-4">
        <?php if ($error): ?>
            <div class="alert alert-danger text-center mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 shadow">Sign In</button>
        </form>

        <div class="text-center mt-4">
            <p class="small text-muted mb-0">Don't have an account? <a href="register.php" class="register-link">Register here</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>