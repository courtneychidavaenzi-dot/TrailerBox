<?php
session_start();
include 'config/db.php';

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userId, $username, $hashedPassword);

        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                mysqli_stmt_close($stmt);
                header("Location: index.php");
                exit;
            }
        }

        mysqli_stmt_close($stmt);
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Trailerbox</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header">
        <h2>Welcome back</h2>
        <p>Sign in to access your Trailerbox account.</p>
    </div>

    <?php if ($error): ?>
        <div class="auth-message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form" novalidate>
        <input type="email" name="email" placeholder="Email (e.g. you@example.com)" required value="<?php echo htmlspecialchars($email); ?>">
        <div class="input-hint">Valid email examples: you@example.com, movie.fan@mail.com</div>
        <input type="password" name="password" placeholder="Password (min 8 characters)" required>
        <button type="submit" name="login">Login</button>
    </form>

    <div class="link-row">
        <span>Don't have an account?</span>
        <a href="register.php">Create one</a>
    </div>
</div>

</body>
</html>