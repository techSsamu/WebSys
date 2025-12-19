<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../forms/login.php');
    exit;
}

// 1. Fetch Summary Counts
$user_counts = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll(PDO::FETCH_KEY_PAIR);
$thesis_status = $pdo->query("SELECT status, COUNT(*) as count FROM thesis GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$total_files = $pdo->query("SELECT COUNT(*) FROM files")->fetchColumn();

// Ensure roles exist in the array to avoid errors
$roles = ['student' => 0, 'reviewer' => 0, 'admin' => 0];
$user_counts = array_merge($roles, $user_counts);

// 2. Fetch Latest 5 Activities
$logs = $pdo->query("SELECT al.*, u.name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY logged_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar { min-height: 100vh; background: #4e73df; color: white; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 15px; }
        .sidebar a.active { background: white; color: #4e73df; font-weight: bold; border-radius: 5px 0 0 5px; }
        .card { border: none; border-radius: 10px; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.5rem; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3 shadow">
            <h5 class="text-center fw-bold mb-4">ADMIN PANEL</h5>
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php"><i class="bi bi-people me-2"></i> Users</a>
            <a href="all_submissions.php"><i class="bi bi-file-earmark-text me-2"></i> All Theses</a>
            <a href="logs.php"><i class="bi bi-list-check me-2"></i> Activity Logs</a>
            <hr>
            <a href="../logout.php" class="text-warning"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>

        <div class="col-md-10 p-4">
            <h3 class="fw-bold text-gray-800 mb-4">System Overview</h3>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm p-3 border-start border-primary border-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary text-white me-3"><i class="bi bi-people"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Students</small>
                                <h4 class="mb-0 fw-bold"><?= $user_counts['student'] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm p-3 border-start border-success border-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success text-white me-3"><i class="bi bi-check-circle"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Approved</small>
                                <h4 class="mb-0 fw-bold"><?= $thesis_status['approved'] ?? 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm p-3 border-start border-warning border-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning text-white me-3"><i class="bi bi-clock-history"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Pending</small>
                                <h4 class="mb-0 fw-bold"><?= $thesis_status['pending'] ?? 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm p-3 border-start border-info border-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info text-white me-3"><i class="bi bi-files"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Total Files</small>
                                <h4 class="mb-0 fw-bold"><?= $total_files ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold">Recent System Activity</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach($logs as $log): ?>
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <small class="fw-bold"><?= htmlspecialchars($log['name'] ?? 'System') ?></small>
                                        <small class="text-muted"><?= date('h:i A', strtotime($log['logged_at'])) ?></small>
                                    </div>
                                    <div class="small text-muted"><?= htmlspecialchars($log['action_performed']) ?></div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="text-center mt-3">
                                <a href="logs.php" class="small text-primary text-decoration-none">View All Logs</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold">Management Shortcuts</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="manage_users.php" class="btn btn-outline-primary text-start p-3">
                                    <i class="bi bi-person-plus me-2"></i> Manage User Accounts
                                </a>
                                <a href="all_submissions.php" class="btn btn-outline-secondary text-start p-3">
                                    <i class="bi bi-search me-2"></i> Audit Research Submissions
                                </a>
                                <button onclick="window.print()" class="btn btn-outline-dark text-start p-3">
                                    <i class="bi bi-printer me-2"></i> Generate System Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>