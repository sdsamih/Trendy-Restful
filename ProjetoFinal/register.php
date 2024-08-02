<?php
session_start();
require 'vendor/autoload.php';
require 'database.php';
require 'User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Verificar se o username já existe
        $existingUser = User::where('username', $username)->first();
        if ($existingUser) {
            $error = 'Username already exists.';
        } else {
            // Criar novo usuário
            $user = User::create([
                'username' => $username,
                'password' => password_hash($password,PASSWORD_BCRYPT)
            ]);

            // Iniciar sessão e redirecionar para o feed
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['cargo'] = $user->cargo;
            header('Location: feed.php');
            
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Twitter Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    body{
        background-color: #89d7d4;
    }
    .register-container{
        background-color: #FDFCF7;
    }
    footer {
            background-color: #0ea8e9;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            margin-top: 100%;
            width: 100%;
        }
</style>
<body>
    <div class="register-container">
    <a>
            <img src="TRENDY.png" alt="Logo trendy" id="logo">
        </a>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrar</button>
        </form>
        <p>Already have an account? <a href="index.html">Login</a></p>
    </div>
    <footer>
        <p>&copy; 2024 TRENDY. Desenvolvido por: Samih Santos e Wyllgner França</p>
    </footer>
</body>
</html>
