<?php

$profile_picture = "angelo.jpg"; 
$full_name = "Mark Angelo V. Del Rosario";
$email = "delrosario.markangelo.v@email.com";
$phone = "09466363929";
$address = "Alcala, Pangasinan";
$date_of_birth = "August 30, 2003";
$occupation = "IT Student";
$nationality = "Filipino";
$gender = "Male";
$linkedin = "Mark Angelo Del Rosario";
$github = "Mark Angelo Del Rosario";


function showValue($value) {
    return $value != "" ? $value : "NA";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $full_name; ?> - Profile</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            margin: 0;
            background: #f0f2f5;
        }
        .resume {
            display: flex;
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .sidebar {
            background: #1E5BA0;
            color: white;
            width: 300px;
            padding: 30px 20px;
            text-align: center;
        }
        .sidebar img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin-bottom: 15px;
            border: 3px solid white;
        }
        .sidebar h1 {
            margin: 5px 0;
            font-size: 22px;
        }
        .sidebar h2 {
            font-size: 15px;
            font-weight: normal;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        .sidebar p {
            font-size: 14px;
            margin: 10px 0;
            line-height: 1.4;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
        }
       
        .main {
            flex: 1;
            padding: 30px;
        }
        .main h3 {
            color: #1E5BA0;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
            margin-top: 20px;
        }
        .main p {
            font-size: 15px;
            line-height: 1.6;
        }
        .main ul {
            margin: 10px 0 20px 20px;
        }
        .skills {
            margin-top: 10px;
        }
        .skills span {
            display: inline-block;
            background: #e8f0fe;
            border: 1px solid #1E5BA0;
            padding: 6px 12px;
            margin: 5px;
            border-radius: 15px;
            font-size: 13px;
            color: #1E5BA0;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="resume">
   
    <div class="sidebar">
        <?php
        if ($profile_picture != "" && file_exists($profile_picture)) {
            echo "<img src='$profile_picture' alt='Profile Picture'>";
        }
        ?>
        <h1><?php echo showValue($full_name); ?></h1>
        <h2><?php echo showValue($occupation); ?></h2>

        <p><strong>Phone:</strong><br><?php echo showValue($phone); ?></p>
        <p><strong>Email:</strong><br><?php echo showValue($email); ?></p>
        <p><strong>LinkedIn:</strong><br><a href="#"><?php echo showValue($linkedin); ?></a></p>
        <p><strong>Github:</strong><br><a href="#"><?php echo showValue($github); ?></a></p>
        <p><strong>Address:</strong><br><?php echo showValue($address); ?></p>
        <p><strong>Date of Birth:</strong><br><?php echo showValue($date_of_birth); ?></p>
        <p><strong>Gender:</strong><br><?php echo showValue($gender); ?></p>
        <p><strong>Nationality:</strong><br><?php echo showValue($nationality); ?></p>
    </div>

    
    <div class="main">
        
        <p>
            IT student with hands-on experience in frontend development. Skilled in HTML, CSS, JavaScript, and modern frameworks like React. Passionate about creating responsive, user-friendly web interfaces.
        </p>

     
       
        
        <h3>Education</h3>

         <p><strong>2022 - Present </strong> – Tirtiary<br>
        <em>Pangasinan State University Urdaneta Campus</em><br>
        <br>
        
        <p><strong>2020–2022</strong> – Senior High School (STEM)<br>
        <em>San Pedro Apartado National High School</em><br><br>

        <p><strong>2015–2020</strong> – Junior High School<br>
        <em>San Pedro Apartado National High School</em><br>
        <br>

        <p><strong>2008–2015</strong> – Primary<br>
        <em>Guinawedan Elementary School</em><br>
        <br>

        <h3>Experience</h3>
        <p><strong>Work Immersion</strong> </p>
        <p><strong>April, 2022</strong><br>
        <em>Tech Support at Department of Public Works and Highways (DPWH) Pangasinan 3rd District Engineering Office</em></p>

        <h3>Skills</h3>
        <div class="skills">
            <span>Visual Basic</span>
            <span>C#, C++</span>
            <span>Java</span>
            <span>HTML</span>
            <span>Web Development</span>
        </div>
    </div>
</div>

</body>
</html>