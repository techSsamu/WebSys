<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $country = $_POST['country'];
    $dob = $_POST['dob'];
    $nationality = $_POST['nationality'];
    $education = $_POST['education'];
    $occupation = $_POST['occupation'];
    $hobbies = $_POST['hobbies'];
    $skills = $_POST['skills'];
    $language = $_POST['language'];
    $experience = $_POST['experience'];
    $reference = $_POST['reference'];

  
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $fileName = $_FILES['photo']['name'];
    $fileTmpName = $_FILES['photo']['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileUploadMessage = '';

  
    if (in_array(strtolower($fileExtension), $allowedExtensions)) {
        $uploadDir = 'uploads/';  
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);  
        }

        $newFileName = time() . '-' . $fileName;  
        
        
        if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
            $fileUploadMessage = "Photo uploaded successfully!";
        } else {
            $fileUploadMessage = "Error uploading photo.";
        }
    } else {
        $fileUploadMessage = "Only JPG, JPEG, PNG, GIF, or PDF files are allowed.";
    }

   
    echo "<h1>Biodata Submission</h1>";
    echo "<p><strong>Name:</strong> $name</p>";
    echo "<p><strong>Age:</strong> $age</p>";
    echo "<p><strong>Gender:</strong> $gender</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Phone:</strong> $phone</p>";
    echo "<p><strong>Address:</strong> $address</p>";
    echo "<p><strong>City:</strong> $city</p>";
    echo "<p><strong>State:</strong> $state</p>";
    echo "<p><strong>ZIP Code:</strong> $zip</p>";
    echo "<p><strong>Country:</strong> $country</p>";
    echo "<p><strong>Date of Birth:</strong> $dob</p>";
    echo "<p><strong>Nationality:</strong> $nationality</p>";
    echo "<p><strong>Education:</strong> $education</p>";
    echo "<p><strong>Occupation:</strong> $occupation</p>";
    echo "<p><strong>Hobbies:</strong> $hobbies</p>";
    echo "<p><strong>Skills:</strong> $skills</p>";
    echo "<p><strong>Languages Spoken:</strong> $language</p>";
    echo "<p><strong>Experience:</strong> $experience</p>";
    echo "<p><strong>Reference:</strong> $reference</p>";

    
    if (isset($newFileName)) {
        echo "<p><strong>Uploaded Photo:</strong><br><img src='$uploadDir$newFileName' alt='Profile Photo' style='width:150px;height:150px;'></p>";
    }

  
    echo "<p>$fileUploadMessage</p>";

} else {
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            width: auto;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Biodata Form</h2>
    <form action="biodata.php" method="post" enctype="multipart/form-data">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="age">Age:</label>
        <input type="number" name="age" id="age" required>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" required>

        <label for="address">Address:</label>
        <textarea name="address" id="address" rows="3" required></textarea>

        <label for="city">City:</label>
        <input type="text" name="city" id="city" required>

        <label for="state">State:</label>
        <input type="text" name="state" id="state" required>

        <label for="zip">ZIP Code:</label>
        <input type="text" name="zip" id="zip" required>

        <label for="country">Country:</label>
        <input type="text" name="country" id="country" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" id="dob" required>

        <label for="nationality">Nationality:</label>
        <input type="text" name="nationality" id="nationality" required>

        <label for="education">Education:</label>
        <input type="text" name="education" id="education" required>

        <label for="occupation">Occupation:</label>
        <input type="text" name="occupation" id="occupation" required>

        <label for="hobbies">Hobbies:</label>
        <input type="text" name="hobbies" id="hobbies" required>

        <label for="skills">Skills:</label>
        <input type="text" name="skills" id="skills" required>

        <label for="language">Languages Spoken:</label>
        <input type="text" name="language" id="language" required>

        <label for="experience">Experience:</label>
        <textarea name="experience" id="experience" rows="3" required></textarea>

        <label for="reference">Reference:</label>
        <input type="text" name="reference" id="reference" required>

        <label for="photo">Upload Photo:</label>
        <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png,.gif,.pdf" required>

        <input type="submit" value="Submit">
    </form>
</div>

</body>
</html>

<?php
}
?>
