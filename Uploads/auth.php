<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/db.php';



if(isset($_POST['login'])){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        switch($user['role']){
            case 'student':
                header("Location: student/student_dashboard.php");
                exit();
            case 'faculty':
                header("Location: faculty/faculty_dashboard.php");
                exit();
            case 'admin':
                header("Location: admin/admin_dashboard.php");
                exit();
        }
    } else {
        $login_error = "Invalid email or password.";
    }
}


if(isset($_POST['register'])){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

   
    $uploads_dir = __DIR__ . '/uploads';
    if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

   
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error']==0){
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profile_image = "uploads/profile_" . time() . "." . $ext;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], __DIR__ . '/' . $profile_image);
    } else $profile_image = null;

    
    if(isset($_FILES['signature']) && $_FILES['signature']['error']==0){
        $ext = pathinfo($_FILES['signature']['name'], PATHINFO_EXTENSION);
        $signature = "uploads/signature_" . time() . "." . $ext;
        move_uploaded_file($_FILES['signature']['tmp_name'], __DIR__ . '/' . $signature);
    } else $signature = null;


    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check->num_rows>0){
        $register_error = "Email already exists.";
    } else {
        $conn->query("INSERT INTO users (name,email,password,role,profile_image,signature) VALUES ('$name','$email','$password','$role','$profile_image','$signature')");
        $register_success = "Account created successfully. You can now login.";
    }
}


if(isset($_GET['logout'])){
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
