<?php
session_start();
include __DIR__ . '/../db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}


if(isset($_POST['add_subject'])){
    $code = $conn->real_escape_string($_POST['code']);
    $name = $conn->real_escape_string($_POST['name']);
    $units = (int)$_POST['units'];
    $prereq = !empty($_POST['prerequisite']) ? (int)$_POST['prerequisite'] : NULL;

    $conn->query("INSERT INTO subjects (code, name, units, prerequisite) VALUES ('$code','$name',$units,".($prereq ?? "NULL").")");
    header("Location: admin_dashboard.php");
    exit();
}


if(isset($_POST['edit_subject'])){
    $id = (int)$_POST['id'];
    $code = $conn->real_escape_string($_POST['code']);
    $name = $conn->real_escape_string($_POST['name']);
    $units = (int)$_POST['units'];
    $prereq = !empty($_POST['prerequisite']) ? (int)$_POST['prerequisite'] : NULL;

    $conn->query("UPDATE subjects SET code='$code', name='$name', units=$units, prerequisite=".($prereq ?? "NULL")." WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}


if(isset($_POST['delete_subject'])){
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM subjects WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}


$subjects = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Subjects Management</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add Subject</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Units</th>
                    <th>Prerequisite</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = $subjects->fetch_assoc()): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['code']) ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= $s['units'] ?></td>
                        <td>
                            <?php
                            if($s['prerequisite']){
                                $pr = $conn->query("SELECT code FROM subjects WHERE id=".$s['prerequisite'])->fetch_assoc();
                                echo htmlspecialchars($pr['code']);
                            } else { echo '-'; }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editSubjectModal<?= $s['id'] ?>">Edit</button>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" name="delete_subject" class="btn btn-sm btn-danger" onclick="return confirm('Delete this subject?')">Delete</button>
                            </form>
                        </td>
                    </tr>

                    
                    <div class="modal fade" id="editSubjectModal<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <form method="POST" class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Subject</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Code</label>
                                <input type="text" name="code" value="<?= htmlspecialchars($s['code']) ?>" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($s['name']) ?>" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Units</label>
                                <input type="number" name="units" value="<?= $s['units'] ?>" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prerequisite</label>
                                <select name="prerequisite" class="form-select">
                                    <option value="">None</option>
                                    <?php
                                    $prereq_subjects = $conn->query("SELECT id, code, name FROM subjects WHERE id != {$s['id']} ORDER BY name ASC");
                                    while($sub = $prereq_subjects->fetch_assoc()):
                                    ?>
                                    <option value="<?= $sub['id'] ?>" <?= $s['prerequisite']==$sub['id']?'selected':'' ?>>
                                        <?= htmlspecialchars($sub['code'].' - '.$sub['name']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" name="edit_subject" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          </div>
                        </form>
                      </div>
                    </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Units</label>
            <input type="number" name="units" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prerequisite</label>
            <select name="prerequisite" class="form-select">
                <option value="">None</option>
                <?php
                $all_subjects = $conn->query("SELECT id, code, name FROM subjects ORDER BY name ASC");
                while($sub = $all_subjects->fetch_assoc()):
                ?>
                <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['code'].' - '.$sub['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_subject" class="btn btn-success">Add Subject</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
