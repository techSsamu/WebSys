<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
    header('Location: ../forms/login.php');
    exit;
}

// Fetch only pending theses
$stmt = $pdo->query("SELECT t.*, u.name as student_name FROM thesis t JOIN users u ON t.student_id = u.id WHERE t.status = 'pending' ORDER BY t.uploaded_at ASC");
$pending_theses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Reviews | Reviewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar { min-height: 100vh; background: #1a1c2e; color: white; position: fixed; width: 250px; }
        .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; display: block; padding: 15px; transition: 0.3s; }
        .sidebar a.active { color: white; background: #1cc88a; font-weight: bold; }
        .main-content { margin-left: 250px; padding: 40px; }
        .thesis-card { border: none; transition: 0.2s; }
        .thesis-card:hover { transform: scale(1.01); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <h5 class="fw-bold mb-0">REVIEWER</h5>
        <hr class="text-white-50">
    </div>
    <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="notifications.php" class="active"><i class="bi bi-bell me-2"></i> Pending Reviews</a>
    <a href="../logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
</div>

<div class="main-content">
    <h3 class="fw-bold mb-4">Pending Evaluations</h3>

    <?php if(empty($pending_theses)): ?>
        <div class="text-center py-5">
            <i class="bi bi-emoji-smile fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">Great job! The queue is empty.</h5>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($pending_theses as $t): ?>
            <div class="col-12 mb-3">
                <div class="card thesis-card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-dark border mb-2 small">New Submission</span>
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($t['title']) ?></h5>
                            <p class="text-muted mb-0 small">Student: <?= htmlspecialchars($t['student_name']) ?> | Uploaded: <?= date('M d, Y', strtotime($t['uploaded_at'])) ?></p>
                        </div>
                        <a href="evaluate.php?id=<?= $t['id'] ?>" class="btn btn-success px-4 rounded-pill">
                            <i class="bi bi-pencil-square me-1"></i> Evaluate
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>