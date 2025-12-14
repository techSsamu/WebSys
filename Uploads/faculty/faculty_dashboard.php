<?php
session_start();
include __DIR__ . '/../db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../login.php");
    exit();
}

if(isset($_POST['submit_grade'])){
    $student_id = (int)$_POST['student_id'];
    $subject_id = (int)$_POST['subject_id'];
    $grade = $conn->real_escape_string($_POST['grade']);

    $check = $conn->query("SELECT * FROM grades WHERE student_id=$student_id AND subject_id=$subject_id");
    if($check->num_rows > 0){
        $conn->query("UPDATE grades SET grade='$grade' WHERE student_id=$student_id AND subject_id=$subject_id");
    } else {
        $conn->query("INSERT INTO grades (student_id, subject_id, grade) VALUES ($student_id, $subject_id, '$grade')");
    }
    $success = "Grade submitted successfully.";
}

$subjects = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faculty Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Enrollment System</a>
    <div class="d-flex">
      <a href="../auth.php?logout=1" class="btn btn-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <h2 class="mb-4">Subjects & Students</h2>

    <?php while($s = $subjects->fetch_assoc()): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <?= htmlspecialchars($s['code']) ?> - <?= htmlspecialchars($s['name']) ?>
        </div>
        <div class="card-body">
            <?php
            $students = $conn->query("
                SELECT u.id as student_id, u.name, u.profile_image, u.signature, g.grade
                FROM enrollments e
                JOIN users u ON e.student_id = u.id
                LEFT JOIN grades g ON g.student_id = u.id AND g.subject_id = e.subject_id
                WHERE e.subject_id = {$s['id']}
            ");
            if($students->num_rows == 0){
                echo "<p class='text-muted'>No students enrolled.</p>";
            } else {
            ?>
            <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>Student</th>
                        <th>Profile</th>
                        <th>Signature</th>
                        <th>Grade</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($st = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($st['name']) ?></td>
                        <td>
                            <?php if($st['profile_image']): ?>
                            <img src="../<?= htmlspecialchars($st['profile_image']) ?>" class="img-thumbnail" width="50">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($st['signature']): ?>
                            <img src="../<?= htmlspecialchars($st['signature']) ?>" class="img-thumbnail" width="50">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($st['grade']) ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="student_id" value="<?= $st['student_id'] ?>">
                                <input type="hidden" name="subject_id" value="<?= $s['id'] ?>">
                                <input type="text" name="grade" value="<?= htmlspecialchars($st['grade']) ?>" class="form-control form-control-sm" placeholder="Grade" required>
                                <button type="submit" name="submit_grade" class="btn btn-sm btn-success">Submit</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
