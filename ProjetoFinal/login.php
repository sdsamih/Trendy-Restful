<?php
session_start();
require 'vendor/autoload.php';
require 'database.php';
require 'User.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta ao banco de dados usando Eloquent para verificar o usuário e a senha
    $user = User::where('username', $username)->first();

    if ($user && password_verify($password, $user->password)) {
        // Usuário encontrado, iniciar sessão
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['cargo'] = $user->cargo;
        header('Location: feed.php'); // Redirecionar para o feed
        exit;
    } else {
        // Usuário ou senha incorretos
        $error = 'Username or password is incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Twitter Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    body{
        background-color: #89d7d4;
    }
    .login-container{
        background-color: #FDFCF7;
    }
</style>
<body>
    <div class="login-container">
        <a>
            <img src="TRENDY.png" alt="Logo trendy" id="logo">
        </a>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Logar</button>
        </form>
        <p>Não tem uma conta? <a href="register.html">Registrar</a></p>
    </div>
</body>
</html>
