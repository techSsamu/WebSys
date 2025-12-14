<?php
session_start();
include __DIR__ . '/../db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: ../login.php");
    exit();
}

$student_id = (int)$_SESSION['user_id'];

/* ================= HANDLE ENROLLMENT ================= */
if(isset($_POST['enroll'])){
    $subject_id = (int)$_POST['subject_id'];

    // Check if already enrolled
    $already = $conn->query("
        SELECT id FROM enrollments 
        WHERE student_id=$student_id AND subject_id=$subject_id
    ");
    if($already->num_rows > 0){
        $error = "You are already enrolled in this subject.";
    } else {

        // Get prerequisite of subject
        $sub = $conn->query("
            SELECT prerequisite FROM subjects WHERE id=$subject_id
        ")->fetch_assoc();

        if($sub && $sub['prerequisite']){
            $prereq = (int)$sub['prerequisite'];

            // Check if prerequisite is completed (enrolled)
            $completed = $conn->query("
                SELECT id FROM enrollments 
                WHERE student_id=$student_id AND subject_id=$prereq
            ");

            if($completed->num_rows == 0){
                $error = "Enrollment blocked: prerequisite subject not completed.";
            } else {
                $conn->query("
                    INSERT INTO enrollments (student_id, subject_id)
                    VALUES ($student_id, $subject_id)
                ");
                $success = "Successfully enrolled!";
            }

        } else {
            // No prerequisite
            $conn->query("
                INSERT INTO enrollments (student_id, subject_id)
                VALUES ($student_id, $subject_id)
            ");
            $success = "Successfully enrolled!";
        }
    }
}

/* ================= FETCH SUBJECTS ================= */
$subjects = $conn->query("
    SELECT s.*, p.code AS prereq_code
    FROM subjects s
    LEFT JOIN subjects p ON s.prerequisite = p.id
    ORDER BY s.name ASC
");

/* ================= FETCH ENROLLED SUBJECTS ================= */
$enrolled = [];
$enrolled_q = $conn->query("
    SELECT subject_id FROM enrollments WHERE student_id=$student_id
");
while($row = $enrolled_q->fetch_assoc()){
    $enrolled[] = $row['subject_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-primary px-3">
    <span class="navbar-brand">Student Dashboard</span>
    <a href="../auth.php?logout=1" class="btn btn-light">Logout</a>
</nav>

<div class="container my-4">

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if(isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<h3 class="mb-3">Available Subjects</h3>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-primary">
<tr>
    <th>Code</th>
    <th>Subject</th>
    <th>Units</th>
    <th>Prerequisite</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php while($s = $subjects->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($s['code']) ?></td>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?= $s['units'] ?></td>
    <td><?= $s['prereq_code'] ?: '-' ?></td>

    <td>
        <?php if(in_array($s['id'], $enrolled)): ?>
            <span class="badge bg-success">Enrolled</span>
        <?php else: ?>
            <span class="badge bg-secondary">Available</span>
        <?php endif; ?>
    </td>

    <td>
        <?php if(!in_array($s['id'], $enrolled)): ?>
            <form method="POST">
                <input type="hidden" name="subject_id" value="<?= $s['id'] ?>">
                <button type="submit" name="enroll" class="btn btn-sm btn-primary">
                    Enroll
                </button>
            </form>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
