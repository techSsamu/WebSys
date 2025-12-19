<?php
session_start();
require '../db.php';

// Access Control: Only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../forms/login.php');
    exit;
}

// Fetch Activity Logs joined with User names for better readability
$query = "SELECT al.*, u.name as user_name, u.role as user_role 
          FROM activity_logs al 
          LEFT JOIN users u ON al.user_id = u.id 
          ORDER BY al.logged_at DESC";

$stmt = $pdo->query($query);
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar { min-height: 100vh; background: #4e73df; color: white; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 15px; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: white; color: #4e73df; font-weight: bold; border-radius: 5px 0 0 5px; }
        .log-card { border: none; border-radius: 12px; }
        .timestamp { font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3">
            <h5 class="text-center fw-bold mb-4">ADMIN PANEL</h5>
            <a href="manage_users.php"><i class="bi bi-people me-2"></i> Users</a>
            <a href="all_submissions.php"><i class="bi bi-file-earmark-text me-2"></i> All Theses</a>
            <a href="logs.php" class="active"><i class="bi bi-list-check me-2"></i> Activity Logs</a>
            <hr>
            <a href="../logout.php" class="text-warning"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-gray-800">System Activity Logs</h3>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Export Report
                </button>
            </div>

            <div class="card log-card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Action Performed</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No activity logs found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="timestamp text-muted">
                                            <?= date('Y-m-d H:i:s', strtotime($log['logged_at'])) ?>
                                        </td>
                                        <td class="fw-bold">
                                            <?= $log['user_name'] ? htmlspecialchars($log['user_name']) : '<span class="text-danger">Unknown/Deleted User</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary opacity-75">
                                                <?= ucfirst($log['user_role'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="bi bi-info-circle text-primary me-2"></i>
                                            <?= htmlspecialchars($log['action_performed']) ?>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($log['ip_address'] ?? '0.0.0.0') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>