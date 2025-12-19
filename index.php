<?php
session_start();
if(!isset($_SESSION['role'])) header('Location: forms/login.html');

if($_SESSION['role']=='student') header('Location: student/my_thesis.php');
elseif($_SESSION['role']=='reviewer') header('Location: reviewer/notifications.php');
elseif($_SESSION['role']=='admin') header('Location: admin/manage_users.php');
