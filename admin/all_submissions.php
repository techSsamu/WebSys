<?php
session_start();
require '../db.php';

// 1. Absolute Access Control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../forms/login.php');
    exit;
}

// 2. Search and Filter Logic
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
$filter_status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

$query = "SELECT t.*, u.name as student_name, f.file_path 
          FROM thesis t 
          JOIN users u ON t.student_id = u.id 
          LEFT JOIN files f ON t.id = f.thesis_id 
          WHERE (t.title LIKE ? OR u.name LIKE ?)";

$params = [$search, $search];

if ($filter_status) {
    $query .= " AND t.status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY t.uploaded_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$all_theses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Submissions | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        /* Fix for fixed sidebar overlapping content */
        .sidebar { min-height: 100vh; background: #4e73df; color: white; position: fixed; width: 250px; z-index: 1000; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 15px; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: white; color: #4e73df; font-weight: bold; }
        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        .card { border: none; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar">
        <div class="p-4 text-center">
            <h5 class="fw-bold mb-0">ADMIN PANEL</h5>
            <hr class="text-white-50">
        </div>
        <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="manage_users.php"><i class="bi bi-people me-2"></i> Users</a>
        <a href="all_submissions.php" class="active"><i class="bi bi-file-earmark-text me-2"></i> All Theses</a>
        <a href="logs.php"><i class="bi bi-list-check me-2"></i> Activity Logs</a>
        <div class="mt-5 px-3">
            <a href="../logout.php" class="btn btn-danger btn-sm w-100"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-gray-800 m-0">Thesis Management</h3>
            
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn btn-primary btn-sm px-3">Search</button>
            </form>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Thesis Details</th>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_theses)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">No submissions found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($all_theses as $t): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= htmlspecialchars($t['title']) ?></div>
                                    <div class="small text-muted">Uploaded: <?= date('M d, Y', strtotime($t['uploaded_at'])) ?></div>
                                </td>
                                <td><?= htmlspecialchars($t['student_name']) ?></td>
                                <td>
                                    <?php 
                                        $badge = 'bg-warning';
                                        if($t['status'] == 'approved') $badge = 'bg-success';
                                        if($t['status'] == 'rejected') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?> rounded-pill px-3"><?= ucfirst($t['status']) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="view_thesis.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                                        <a href="delete_thesis.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Delete permanently?')"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>