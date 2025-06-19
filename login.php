<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  if ($username === "admin" && $password === "admin1234") {
    $_SESSION['user'] = $username;
    header("Location: dashboard.php");
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SIMS - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    :root {
      --violet-main: #7c3aed;
      --violet-dark: #5b21b6;
      --violet-light: #a78bfa;
    }
    body {
      background: linear-gradient(120deg, var(--violet-main) 0%, var(--violet-light) 100%);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-container {
      background: #fff;
      padding: 2.5rem 2rem 2rem 2rem;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(124, 58, 237, 0.18);
      width: 100%;
      max-width: 370px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      animation: fadeIn 0.7s;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px);}
      to { opacity: 1; transform: translateY(0);}
    }
    .logo-circle {
      background: linear-gradient(135deg, var(--violet-main) 60%, var(--violet-light) 100%);
      width: 68px;
      height: 68px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.2rem;
      box-shadow: 0 2px 12px rgba(124, 58, 237, 0.12);
    }
    .logo-circle i {
      color: #fff;
      font-size: 2.2rem;
    }
    .login-container h2 {
      margin-bottom: 1.5rem;
      color: var(--violet-main);
      text-align: center;
      font-weight: 700;
      letter-spacing: 1px;
    }
    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .form-group {
      width: 100%;
      display: flex;
      flex-direction: column;
    }
    .form-group label {
      margin-bottom: 0.4rem;
      font-weight: 600;
      color: #444;
      letter-spacing: 0.5px;
    }
    .form-group input {
      width: 100%;
      padding: 0.65rem 0.9rem;
      border: 1.5px solid #e0e6ed;
      border-radius: 7px;
      font-size: 1rem;
      background: #f8fbfd;
      transition: border 0.2s;
      outline: none;
      box-sizing: border-box;
    }
    .form-group input:focus {
      border-color: var(--violet-main);
      background: #fff;
    }
    .btn {
      width: 100%;
      padding: 0.75rem;
      background: linear-gradient(90deg, var(--violet-main) 60%, var(--violet-light) 100%);
      color: #fff;
      border: none;
      border-radius: 7px;
      font-weight: 600;
      font-size: 1.08rem;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px rgba(124, 58, 237, 0.10);
      letter-spacing: 0.5px;
      display: block;
      margin-top: 0.2rem;
    }
    .btn:hover {
      background: linear-gradient(90deg, var(--violet-dark) 60%, var(--violet-main) 100%);
      box-shadow: 0 4px 16px rgba(124, 58, 237, 0.13);
    }
    .error {
      background: #e74c3c;
      color: #fff;
      padding: 0.7rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      text-align: center;
      font-size: 0.98rem;
      width: 100%;
      box-sizing: border-box;
      letter-spacing: 0.5px;
    }
    .login-footer {
      margin-top: 1.5rem;
      text-align: center;
      color: #aaa;
      font-size: 0.97rem;
      letter-spacing: 0.2px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="logo-circle">
      <i class="material-icons">school</i>
    </div>
    <h2>SIMS Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required autofocus />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required />
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
    <div class="login-footer">
       Student Information Management System
    </div>
  </div>
</body>
</html>