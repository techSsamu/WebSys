<?php
session_start();
require '../db.php';

// Access Control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
    header('Location: ../forms/login.php');
    exit;
}

$error = '';
$success = '';

// Get Thesis ID from URL
if (!isset($_GET['id'])) {
    header('Location: notifications.php');
    exit;
}

$thesis_id = $_GET['id'];

// Fetch Thesis details
$stmt = $pdo->prepare("SELECT t.*, u.name as student_name FROM thesis t JOIN users u ON t.student_id = u.id WHERE t.id = ?");
$stmt->execute([$thesis_id]);
$thesis = $stmt->fetch();

if (!$thesis) {
    die("Thesis not found.");
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $comment = trim($_POST['comment']);
    $reviewer_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // 1. Update status in 'thesis' table
        $updateStmt = $pdo->prepare("UPDATE thesis SET status = ? WHERE id = ?");
        $updateStmt->execute([$status, $thesis_id]);

        // 2. Insert comment into 'comments' table (ensure you have this table)
        $commentStmt = $pdo->prepare("INSERT INTO comments (thesis_id, reviewer_id, comment) VALUES (?, ?, ?)");
        $commentStmt->execute([$thesis_id, $reviewer_id, $comment]);

        // 3. Log the activity
        $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action_performed, ip_address) VALUES (?, ?, ?)");
        $logAction = "Reviewer " . ($status == 'approved' ? 'Approved' : 'Rejected') . " Thesis ID: " . $thesis_id;
        $logStmt->execute([$reviewer_id, $logAction, $_SERVER['REMOTE_ADDR']]);

        $pdo->commit();
        $success = "Evaluation submitted successfully!";
        header("refresh:2;url=notifications.php");
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Thesis | Reviewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; }
        .eval-card { border: none; border-radius: 15px; }
        .btn-approve { background-color: #1cc88a; color: white; }
        .btn-reject { background-color: #e74a3b; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card eval-card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-success">Reviewing: <?= htmlspecialchars($thesis['title']) ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php if($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>
                    <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>

                    <div class="row">
                        <div class="col-md-7 border-end">
                            <h6><strong>Student:</strong> <?= htmlspecialchars($thesis['student_name']) ?></h6>
                            <h6><strong>Keywords:</strong> <span class="badge bg-light text-dark border"><?= htmlspecialchars($thesis['keywords']) ?></span></h6>
                            <hr>
                            <h6><strong>Abstract:</strong></h6>
                            <p class="text-muted" style="text-align: justify;"><?= nl2br(htmlspecialchars($thesis['abstract'])) ?></p>
                        </div>

                        <div class="col-md-5 ps-4">
                            <form action="" method="POST">
                                <label class="form-label fw-bold">Decision</label>
                                <select name="status" class="form-select mb-3" required>
                                    <option value="" disabled selected>Select Result...</option>
                                    <option value="approved">Approve Submission</option>
                                    <option value="rejected">Reject / Needs Revision</option>
                                </select>

                                <label class="form-label fw-bold">Reviewer Comments</label>
                                <textarea name="comment" class="form-control mb-4" rows="8" placeholder="Type your feedback here for the student..." required></textarea>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success fw-bold">Submit Evaluation</button>
                                    <a href="notifications.php" class="btn btn-light border text-muted">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>