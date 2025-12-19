<?php
session_start();
require '../db.php';

// Access Control: Only students can view their own comments
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../forms/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: student.php');
    exit;
}

$thesis_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// 1. Fetch Thesis details (and verify it belongs to this student)
$stmt = $pdo->prepare("SELECT * FROM thesis WHERE id = ? AND student_id = ?");
$stmt->execute([$thesis_id, $user_id]);
$thesis = $stmt->fetch();

if (!$thesis) {
    die("Thesis not found or access denied.");
}

// 2. Fetch all comments for this thesis, including Reviewer names
$commentStmt = $pdo->prepare("
    SELECT c.*, u.name as reviewer_name 
    FROM comments c 
    JOIN users u ON c.reviewer_id = u.id 
    WHERE c.thesis_id = ? 
    ORDER BY c.commented_at DESC
");
$commentStmt->execute([$thesis_id]);
$comments = $commentStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Feedback | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .feedback-card { border: none; border-radius: 15px; }
        .comment-bubble { background: #ffffff; border-left: 5px solid #4e73df; border-radius: 8px; }
        .status-header { border-radius: 15px 15px 0 0; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="student.php" class="btn btn-link text-decoration-none text-muted mb-3 p-0">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>

            <div class="card feedback-card shadow-sm">
                <?php 
                    $status = $thesis['status'];
                    $bg = 'bg-warning';
                    if($status == 'approved') $bg = 'bg-success';
                    if($status == 'rejected') $bg = 'bg-danger';
                ?>
                <div class="card-header <?= $bg ?> text-white py-4 status-header">
                    <h4 class="mb-0 fw-bold"><?= htmlspecialchars($thesis['title']) ?></h4>
                    <p class="mb-0 opacity-75 text-uppercase small fw-bold">Current Status: <?= ucfirst($status) ?></p>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text me-2"></i>Reviewer Feedback</h5>

                    <?php if (empty($comments)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-hourglass-split display-4 text-muted"></i>
                            <p class="mt-3 text-muted">No comments have been posted yet. Your thesis is likely still in the queue.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <div class="comment-bubble p-4 mb-4 shadow-sm border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($c['reviewer_name']) ?>
                                    </h6>
                                    <small class="text-muted"><?= date('M d, Y h:i A', strtotime($c['commented_at'])) ?></small>
                                </div>
                                <hr>
                                <p class="mb-0 text-dark" style="white-space: pre-wrap;"><?= htmlspecialchars($c['comment']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="card-footer bg-white border-0 p-4 text-center">
                    <?php if($status == 'rejected'): ?>
                        <div class="alert alert-info border-0 mb-0">
                            <strong>Notice:</strong> Please revise your document based on the feedback above and submit a new version.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>