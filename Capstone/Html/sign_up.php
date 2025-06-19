<?php
include 'config.php';
session_start();

$message = '';

if (isset($_POST['submit'])) {
  $username = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $user_type = $_POST['user_type'];

  // Check if passwords match
  if ($password !== $confirm_password) {
    $message = "Passwords do not match!";
  } else {
    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
      $message = "Email already exists!";
    } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $hashedConfirm = password_hash($confirm_password, PASSWORD_DEFAULT); // Optional if storing confirm_password

      // ✅ Only use confirm_password in query if your DB has that column
      $stmt = $conn->prepare("INSERT INTO users (name, email, password, conf, type) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $username, $email, $hashedPassword, $hashedConfirm, $user_type);
      $stmt->execute();
      $message = "Sign up successful!";
      header("Location: Login.php");
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up - MatchMyBuy</title>
  <link rel="stylesheet" href="../Css/Login.css" />
</head>
<body>
  <div class="logo-wrapper">
    <img src="../Image/logo.png" alt="MatchMyBuy Logo" class="nav-logo" />
  </div>

  <div class="container">
    <h1>Welcome to MatchMyBuy</h1>

    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <div class="login-box">
      <h3>Sign Up</h3>
      <form method="post" action="">
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="email@your-company.com" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required />
        <select name="user_type" required>
          <option value="" disabled selected>Select Type</option>
          <option value="manager">Manager</option>
          <option value="user">User</option>
        </select>
        <input type="submit" name="submit" value="Sign Up" />
      </form>
      <p class="signup-text">Already have an account? <a href="Login.php">Sign In</a> here</p>
    </div>
  </div>
</body>
</html>