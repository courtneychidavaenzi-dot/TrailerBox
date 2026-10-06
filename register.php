<?php
include 'config/db.php';

$message = "";
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $message = "Email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);

            if (mysqli_stmt_execute($insertStmt)) {
                $message = "Registration successful! You can now log in.";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($insertStmt);
        }

        mysqli_stmt_close($checkStmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Trailerbox</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header">
        <h2>Create your account</h2>
        <p>Join Trailerbox and start saving favorites.</p>
    </div>

    <?php if ($message): ?>
        <div class="auth-message <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="auth-form" novalidate>
        <input type="text" name="username" placeholder="Username (e.g. moviefan123)" required value="<?php echo htmlspecialchars($username); ?>">
        <input type="email" name="email" placeholder="Email (e.g. you@example.com)" required value="<?php echo htmlspecialchars($email); ?>">
        <div class="input-hint">Valid email examples: you@example.com, movie.fan@mail.com</div>
        <input type="password" name="password" placeholder="Password (min 8 characters)" required>
        <button type="submit">Register</button>
    </form>

    <div class="link-row">
        <span>Already have an account?</span>
        <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>