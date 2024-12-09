<?php
session_start();
require('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['Name'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $password = trim($_POST['Password'] ?? '');
    $confirmPassword = trim($_POST['ConfirmPassword'] ?? '');
    $phoneNumber = trim($_POST['PhoneNumber'] ?? '');
    $rollno = trim($_POST['RollNo'] ?? '');
    $category = trim($_POST['Category'] ?? '');

    if (!$name || !$email || !$password || !$confirmPassword || !$phoneNumber || !$rollno || !$category) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        $check_sql = "SELECT * FROM user WHERE RollNo = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('s', $rollno);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Roll Number already exists.');</script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $type = ($category === 'LIBRARIAN') ? 'Librarian' : 'Student';
            $insert_sql = "INSERT INTO user (Name, Type, Category, RollNo, EmailId, MobNo, Password) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('sssssss', $name, $type, $category, $rollno, $email, $phoneNumber, $hashed_password);

            if ($insert_stmt->execute()) {
                echo "<script>alert('Registration successful! You will be redirected to the login page.');</script>";
                echo "<script>window.location.href = 'http://borroweaselms.com';</script>";
                exit;
            } else {
                echo "<script>alert('Error: Could not create account.');</script>";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title> 
    <link rel="stylesheet" href="css/signupcss.css">
</head>
<body>
  <div class="wrapper">
    <h2>Registration</h2>
    <form action="signup.php" method="post">
      <div class="input-box">
        <input type="text" name="Name" placeholder="Name" required>
      </div>
      <div class="input-box">
        <input type="email" name="Email" placeholder="Email" required>
      </div>
      <div class="input-box">
        <input type="password" name="Password" placeholder="Password" required>
      </div>
      <div class="input-box">
        <input type="password" name="ConfirmPassword" placeholder="Confirm Password" required>
      </div>
      <div class="input-box">
        <input type="text" name="PhoneNumber" placeholder="Phone Number" required>
      </div>
      <div class="input-box">
        <input type="text" name="RollNo" placeholder="Roll Number" required>
      </div>
      <select name="Category" required>
        <option value="" disabled selected>Select Role</option>
        <option value="STUDENT">Student</option>
        <option value="LIBRARIAN">Librarian</option>
      </select>
      <div class="policy">
        <input type="checkbox" name="Terms" required>
        <h3>I accept all terms & conditions</h3>
      </div>
      <div class="input-box button">
        <button type="submit" name="signup">Sign Up</button>
      </div>
      <div class="text">
        <h3>Already have an account? <a href="http://borroweaselms.com">Login now</a></h3>
      </div>
    </form>
  </div>
</body>
</html>
