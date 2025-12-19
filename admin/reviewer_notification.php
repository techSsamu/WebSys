<?php
session_start();
require '../db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
    header('Location: ../forms/login.php');
    exit;
}



$query = "SELECT t.*, u.name as student_name, f.file_path 
          FROM thesis t 
          JOIN users u ON t.student_id = u.id 
          LEFT JOIN files f ON t.id = f.thesis_id 
          WHERE t.status IN ('pending', 'under_review')
          ORDER BY t.uploaded_at ASC";

$stmt = $pdo->query($query);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviewer Notifications | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; }
        .notification-card { border: none; border-radius: 12px; transition: transform 0.2s; }
        .notification-card:hover { transform: translateY(-3px); }
        .sidebar { min-height: 100vh; background: #1cc88a; color: white; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: white; color: #1cc88a; font-weight: bold; }
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3 shadow">
            <h5 class="text-center fw-bold mb-4">REVIEWER PORTAL</h5>
            <a href="notifications.php" class="active"><i class="bi bi-bell me-2"></i> Notifications</a>
            <a href="history.php"><i class="bi bi-archive me-2"></i> Review History</a>
            <hr>
            <a href="../logout.php" class="text-white"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Incoming Submissions</h3>
                <div class="bg-white p-2 px-3 rounded shadow-sm">
                    <span class="status-dot bg-warning me-2"></span>
                    <small class="fw-bold text-muted"><?= count($notifications) ?> Pending Reviews</small>
                </div>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-check2-circle display-1 text-success"></i>
                    <h4 class="mt-3 text-muted">All caught up!</h4>
                    <p>There are no new thesis submissions requiring your attention.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($notifications as $n): ?>
                    <div class="col-12 mb-3">
                        <div class="card notification-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 fw-bold"><?= htmlspecialchars($n['title']) ?></h5>
                                    <div class="text-muted small">
                                        <i class="bi bi-person me-1"></i> Submitted by: <strong><?= htmlspecialchars($n['student_name']) ?></strong> | 
                                        <i class="bi bi-calendar3 me-1"></i> Date: <?= date('M d, Y', strtotime($n['uploaded_at'])) ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="../<?= $n['file_path'] ?>" class="btn btn-outline-danger btn-sm me-2" target="_blank">
                                        <i class="bi bi-file-pdf"></i> View PDF
                                    </a>
                                    <a href="evaluate.php?id=<?= $n['id'] ?>" class="btn btn-success btn-sm px-4">
                                        Evaluate <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>