<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../forms/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch counts for the stats cards
$stats_stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM thesis WHERE student_id = ?
");
$stats_stmt->execute([$user_id]);
$stats = $stats_stmt->fetch();

// Fetch thesis list
$stmt = $pdo->prepare("SELECT * FROM thesis WHERE student_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$user_id]);
$my_theses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-width: 280px; }
        body { background-color: #f8f9fc; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: #fff; border-right: 1px solid #e3e6f0; }
        .main-content { margin-left: var(--sidebar-width); padding: 2rem; }
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .stat-card { border-left: 0.25rem solid !important; }
        .nav-link { color: #4e73df; padding: 0.75rem 1.5rem; border-radius: 0.5rem; margin: 0.2rem 1rem; }
        .nav-link.active { background: #4e73df; color: #fff !important; }
        .nav-link:hover:not(.active) { background: #f8f9fc; }
        .profile-section { padding: 2rem 1rem; text-align: center; border-bottom: 1px solid #f1f1f1; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="profile-section">
        <img src="../<?= htmlspecialchars($_SESSION['profile_pic'] ?: 'profiles/default.png') ?>" 
             class="rounded-circle mb-3 border" width="80" height="80" style="object-fit: cover;">
        <h6 class="fw-bold mb-0"><?= htmlspecialchars($_SESSION['name']) ?></h6>
        <small class="text-muted text-uppercase">Student Researcher</small>
    </div>
    <div class="nav flex-column mt-3">
        <a href="student.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="upload_thesis.php" class="nav-link"><i class="bi bi-cloud-arrow-up me-2"></i> Submit Thesis</a>
        <a href="view_comments.php" class="nav-link"><i class="bi bi-chat-dots me-2"></i> Feedback</a>
        <hr class="mx-3">
        <a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-gray-800">Research Overview</h4>
        <a href="upload_thesis.php" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> New Submission
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-primary p-3">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Submissions</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['total'] ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-success p-3">
                <div class="text-xs fw-bold text-success text-uppercase mb-1">Approved</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['approved'] ?: 0 ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-danger p-3">
                <div class="text-xs fw-bold text-danger text-uppercase mb-1">Rejected</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['rejected'] ?: 0 ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Recent Submissions</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Thesis Title</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($my_theses as $t): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($t['title']) ?></td>
                            <td class="text-muted"><?= date('M d, Y', strtotime($t['uploaded_at'])) ?></td>
                            <td>
                                <?php 
                                    $badge = 'bg-warning';
                                    if($t['status'] == 'approved') $badge = 'bg-success';
                                    if($t['status'] == 'rejected') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge ?> rounded-pill px-3">
                                    <?= ucfirst($t['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_comments.php?id=<?= $t['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($my_theses)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>