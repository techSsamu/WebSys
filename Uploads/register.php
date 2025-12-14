<?php include __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Enrollment System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">Enrollment System</div>

<div class="card">
  <h2>Register</h2>
  <?php if(isset($register_error)) echo "<p class='error'>$register_error</p>"; ?>
  <?php if(isset($register_success)) echo "<p class='success'>$register_success</p>"; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role" required>
      <option value="student">Student</option>
      <option value="faculty">Faculty</option>
      <option value="admin">Admin</option>
    </select>
    <label>Profile Image:</label>
    <input type="file" name="profile_image" required>
    <label>Signature:</label>
    <input type="file" name="signature" required>
    <button type="submit" name="register">Register</button>
  </form>
  <p class="link">Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>
