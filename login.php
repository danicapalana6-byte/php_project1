<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $valid_user = "Tindahan ni Lola";
    $valid_pass = "LolaLola";

    if ($username === $valid_user && $password === $valid_pass) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Incorrect username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Tindahan ni Lola</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow p-4" style="width: 350px;">
    <h4 class="text-center mb-3">🛒 Tindahan ni Lola</h4>
    <h6 class="text-center text-muted mb-4">Inventory Login</h6>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center p-2"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-dark w-100">Login</button>
    </form>
</div>

</body>
</html>
