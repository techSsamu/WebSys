<?php
session_start();
require '../db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    
    $profile = $_FILES['profile']['name'];
    $signature = $_FILES['signature']['name'];
    $profile_tmp = $_FILES['profile']['tmp_name'];
    $signature_tmp = $_FILES['signature']['tmp_name'];

    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $error = "Email already exists!";
    } else {
        
        $upload_base = '../'; 
        $profile_folder = $upload_base . 'profiles/';
        $signature_folder = $upload_base . 'signatures/';

        
        if (!is_dir($profile_folder)) mkdir($profile_folder, 0777, true);
        if (!is_dir($signature_folder)) mkdir($signature_folder, 0777, true);

        
        $p_ext = pathinfo($profile, PATHINFO_EXTENSION);
        $s_ext = pathinfo($signature, PATHINFO_EXTENSION);
        
        $profile_fn = time() . '_profile.' . $p_ext;
        $signature_fn = time() . '_sign.' . $s_ext;

       
        $profile_db = 'profiles/' . $profile_fn;
        $signature_db = 'signatures/' . $signature_fn;

        if (move_uploaded_file($profile_tmp, $profile_folder . $profile_fn) &&
            move_uploaded_file($signature_tmp, $signature_folder . $signature_fn)) {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password, role, profile_pic, signature) VALUES (?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $email, $hash, $role, $profile_db, $signature_db])) {
                $success = "Account created! You can now log in.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        } else {
            $error = "Failed to upload images. Check folder permissions.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { border: none; border-radius: 1.25rem; background: rgba(255, 255, 255, 0.95); box-shadow: 0 20px 40px rgba(0,0,0,0.2); width: 100%; max-width: 500px; }
        .form-label { font-size: 0.8rem; font-weight: 600; color: #4e73df; text-transform: uppercase; }
        .btn-primary { background: #4e73df; border: none; padding: 0.8rem; font-weight: 600; }
        .btn-primary:hover { background: #224abe; }
    </style>
</head>
<body>

<div class="card shadow">
    <div class="card-header bg-white border-0 pt-4 text-center">
        <h4 class="mb-0">Create Account</h4>
        <small class="text-muted">Thesis Management System</small>
    </div>
    <div class="card-body p-4">
        <?php if ($success): ?>
            <div class="alert alert-success small text-center"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger small text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="student">Student</option>
                        <option value="reviewer">Reviewer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Digital Signature</label>
                <input type="file" name="signature" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 shadow">Register Now</button>
        </form>
        <div class="text-center mt-3">
            <small>Already have an account? <a href="login.php" class="text-decoration-none">Login</a></small>
        </div>
    </div>
</div>

</body>
</html>