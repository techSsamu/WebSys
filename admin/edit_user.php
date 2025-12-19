<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../forms/login.php');
    exit;
}

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) { die("User not found."); }

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];

    try {
        if (!empty($new_password)) {
            // Update name, role, AND password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, role = ?, password = ? WHERE id = ?";
            $params = [$name, $role, $hashed, $id];
        } else {
            // Update only name and role
            $sql = "UPDATE users SET name = ?, role = ? WHERE id = ?";
            $params = [$name, $role, $id];
        }

        $pdo->prepare($sql)->execute($params);
        $msg = "User updated successfully!";
        header("refresh:1;url=manage_users.php");
    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">Edit User Account</h5>
                </div>
                <div class="card-body p-4">
                    <?php if($msg): ?> <div class="alert alert-info"><?= $msg ?></div> <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Email (Fixed)</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">System Role</label>
                            <select name="role" class="form-select">
                                <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                                <option value="reviewer" <?= $user['role'] == 'reviewer' ? 'selected' : '' ?>>Reviewer</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-danger">Reset Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="manage_users.php" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>