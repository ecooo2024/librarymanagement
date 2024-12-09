<?php
session_start();
require('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure RollNo and Password exist
    $u = isset($_POST['RollNo']) ? trim($_POST['RollNo']) : null;
    $p = isset($_POST['Password']) ? trim($_POST['Password']) : null;

    if (!$u || !$p) {
        echo "<script>alert('RollNo and Password are required!');</script>";
        return;
    }

    if (isset($_POST['signin'])) {
        // Use parameterized query
        $sql = "SELECT * FROM user WHERE RollNo = ? OR rfid_card = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $u, $u);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result && $row = $result->fetch_assoc()) {
            $dbPassword = $row['Password'];
    
            // Check if password is required for RFID login
            if (isset($row['rfid_card']) && $row['rfid_card'] === $u) {
                // RFID card login successful
                $_SESSION['RollNo'] = $row['RollNo'];
            } elseif (password_verify($p, $dbPassword)) {
                // Password-based login successful
                $_SESSION['RollNo'] = $row['RollNo'];
            } elseif ($dbPassword === $p) {
                // Login with plain password; hash it now
                $hashedPassword = password_hash($p, PASSWORD_BCRYPT);
                $updateSql = "UPDATE user SET Password = ? WHERE RollNo = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param('ss', $hashedPassword, $row['RollNo']);
                $updateStmt->execute();
    
                $_SESSION['RollNo'] = $row['RollNo'];
            } else {
                echo "<script>alert('Failed to Login! Incorrect credentials.');</script>";
                return;
            }
    
            // Redirect based on user role
            $redirect = ($row['Type'] === 'Admin') 
                        ? 'admin/index.php' 
                        : (($row['Category'] === 'LIBRARIAN') 
                            ? 'librarian/index.php' 
                            : 'student/index.php');
            header("Location: $redirect");
            exit;
        } else {
            echo "<script>alert('Failed to Login! RollNo or RFID not found.');</script>";
        }
    }    
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>BorrowEase</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="./assets/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/uf-style.css">
</head>
<body>
<script>
        function forgotPassword() {
            alert("Please contact the admin to reset your password.");
        }
        </script>

    <!-- Role Selection -->
    <div class="uf-form-signin">
      <div class="text-center">  
        <a href="http://borroweaselms.com"><img src="./assets/images/logo.png" alt="" width="100" height="100"></a>
    <h1 style="color:white;">User Login</style></h1>
    
</div>

    <div class="form-container" id="roleForm"></style>
        <form action="" method="post">
    <form class="mt-4">
        <div class="input-group uf-input-group input-group-lg mb-3">
          <span class="input-group-text fa fa-user"></span>
          <input type="text" name="RollNo" class="form-control" placeholder="RollNo or RFID Card" required>
        </div>
    <div class="input-group uf-input-group input-group-lg mb-3">
        <span class="input-group-text fa fa-lock"></span>
          <input type="password" name="Password" class="form-control" placeholder="Password" required>
        </div>

    <div class="d-flex mb-3 justify-content-between">
        <div class="form-check">
            <input type="checkbox" class="form-check-input uf-form-check-input" id="exampleCheck1">
            <label class="form-check-label text-white" for="exampleCheck1">Remember Me</label>
        </div>
            <a href="#" class="forgot-password" onclick="forgotPassword()">Forgot Password?</a>
        </div>    
        <div class="d-grid mb-4">
          <button type="submit" name="signin" class="btn uf-btn-primary btn-lg">Login</button>
        </div>
            
        
            <div class="uf-social-login d-flex justify-content-center">
          <a href="#" class="uf-social-ic" title="Login with Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="uf-social-ic" title="Login with Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" class="uf-social-ic" title="Login with Google"><i class="fab fa-google"></i></a>
        </div>

        <div class="mt-4 text-center">
          <span class="text-white">Don't have an account?</span>
        <a href="signup.php" class="role-button">Signup</a>
        
</div>
    </div>
</form>
</body>
</html>
