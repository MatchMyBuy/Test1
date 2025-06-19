<?php
include 'config.php';
session_start();

$message = '';

if (isset($_POST['submit'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];

  $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('query failed');

  if (mysqli_num_rows($select_users) > 0) {
    $row = mysqli_fetch_assoc($select_users);

    // ✅ Check password against hashed password in DB
    if (password_verify($password, $row['Password'])) {
      if ($row['Type'] == 'manager') {
        $_SESSION['manager_name'] = $row['name'];
        $_SESSION['manager_email'] = $row['email'];
        $_SESSION['manager_id'] = $row['id'];
        header("Location: Dashboard.html");
        exit();
      } elseif ($row['Type'] == 'user') {
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_id'] = $row['id'];
        header("Location: Dashboard.html");
        exit();
      }
    } else {
      $message = "Incorrect email or password!";
    }
  } else {
    $message = "Incorrect email or password!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - MatchMyBuy</title>
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
  <h3>Login</h3>
  <p>Enter your email to sign in</p>

  <form action="" method="post">
    <input type="email" name="email" placeholder="email@your-company.com" required />
    <input type="password" name="password" placeholder="password" required />
    <button type="submit" name="submit">Sign In</button>
  </form>

  <p class="signup-text">New User? <a href="sign_up.php">Sign Up</a> here</p>
</div>

  </div>
</body>
</html>