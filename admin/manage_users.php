<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../forms/login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$id]);
    header("Location: manage_users.php?msg=deleted");
    exit;
}

$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
$stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
$stmt->execute([$search, $search]);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar { min-height: 100vh; background: #4e73df; color: white; position: fixed; width: inherit; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 15px; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: white; color: #4e73df; font-weight: bold; border-radius: 5px 0 0 5px; }
        .main-content { margin-left: 16.66667%; } /* Offset for col-md-2 */
        .card { border: none; border-radius: 10px; }
        .user-img { object-fit: cover; border-radius: 50%; border: 2px solid #e3e6f0; }
        .search-bar { border-radius: 20px; padding-left: 40px; }
        .search-icon { position: absolute; left: 15px; top: 10px; color: #aaa; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3">
            <h5 class="text-center fw-bold mb-4">ADMIN PANEL</h5>
            <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="active"><i class="bi bi-people me-2"></i> Users</a>
            <a href="all_submissions.php"><i class="bi bi-file-earmark-text me-2"></i> All Theses</a>
            <a href="logs.php"><i class="bi bi-list-check me-2"></i> Activity Logs</a>
            <hr>
            <a href="../logout.php" class="text-warning"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>

        <div class="col-md-10 p-4 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-gray-800">User Management</h3>
                
                <form class="d-flex position-relative w-25" method="GET">
                    <i class="bi bi-search search-icon"></i>
                    <input class="form-control search-bar" type="text" name="search" placeholder="Search name or email..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </form>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Action completed successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="dismiss" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Profile</th>
                                <th>Full Name</th>
                                <th>Email Address</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../<?= htmlspecialchars($u['profile_pic'] ?: 'profiles/default.png') ?>" class="user-img" width="40" height="40" onerror="this.src='https://via.placeholder.com/40'">
                                </td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php 
                                        $badge = 'bg-primary';
                                        if($u['role'] == 'admin') $badge = 'bg-dark';
                                        if($u['role'] == 'reviewer') $badge = 'bg-info';
                                    ?>
                                    <span class="badge <?= $badge ?> rounded-pill px-3">
                                        <?= ucfirst($u['role']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td class="text-center pe-4">
                                    <?php if($u['role'] !== 'admin'): ?>
                                        <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-light border text-primary me-1" title="Edit User">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Permanently delete this user?')" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Protected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>