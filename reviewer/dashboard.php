<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
    header('Location: ../forms/login.php');
    exit;
}

$reviewer_id = $_SESSION['user_id'];

// Get stats
$pending = $pdo->query("SELECT COUNT(*) FROM thesis WHERE status = 'pending'")->fetchColumn();
$my_evaluations = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE reviewer_id = ?");
$my_evaluations->execute([$reviewer_id]);
$done = $my_evaluations->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviewer Dashboard | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --reviewer-theme: #1cc88a; }
        body { background-color: #f8f9fc; }
        .sidebar { min-height: 100vh; background: #1a1c2e; color: white; position: fixed; width: 250px; }
        .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; display: block; padding: 15px; transition: 0.3s; }
        .sidebar a:hover { color: white; background: rgba(255,255,255,0.05); }
        .sidebar a.active { color: white; background: var(--reviewer-theme); font-weight: bold; }
        .main-content { margin-left: 250px; padding: 40px; }
        .card-stat { border: none; border-bottom: 4px solid var(--reviewer-theme); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <img src="../<?= $_SESSION['profile_pic'] ?: 'profiles/default.png' ?>" class="rounded-circle mb-3 border border-2 border-success" width="70" height="70" style="object-fit:cover;">
        <h6 class="fw-bold mb-0"><?= htmlspecialchars($_SESSION['name']) ?></h6>
        <small class="text-success fw-bold">Reviewer</small>
    </div>
    <hr class="mx-3 opacity-25">
    <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="notifications.php"><i class="bi bi-bell me-2"></i> Pending Reviews</a>
    <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
</div>

<div class="main-content">
    <h3 class="fw-bold mb-4">Reviewer Overview</h3>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-stat shadow-sm p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Queue</div>
                        <h2 class="fw-bold mb-0 text-warning"><?= $pending ?></h2>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 text-light"></i>
                </div>
                <div class="mt-3 small text-muted">Papers awaiting evaluation</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat shadow-sm p-4" style="border-bottom-color: #4e73df;">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Completed</div>
                        <h2 class="fw-bold mb-0 text-primary"><?= $done ?></h2>
                    </div>
                    <i class="bi bi-check2-all fs-1 text-light"></i>
                </div>
                <div class="mt-3 small text-muted">Theses you have evaluated</div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0">Recent Submissions</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Head over to the <strong>Pending Reviews</strong> tab to start evaluating the latest student submissions.</p>
                    <a href="notifications.php" class="btn btn-success btn-sm px-4 rounded-pill">View All Pending</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>