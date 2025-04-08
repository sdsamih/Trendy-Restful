<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Twitter Clone</title>
    <link rel="stylesheet" href="pages/assets/style.css">
</head>
<style>
    body{
        background-color: #89d7d4;
    }
    .login-container{
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
    <div class="login-container">
        <a>
            <img src="pages/assets/TRENDY.png" alt="Logo trendy" id="logo">
        </a>
        <form action="pages/login.php" method="POST">
            <div class="form-group">
                <label for="username">Login</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Não tem uma conta? <a href="pages/register.php">Registrar</a></p>
    </div>
    <footer>
        <p>&copy; 2024 TRENDY. Desenvolvido por: Samih Santos e Wyllgner França</p>
    </footer>
</body>
</html>
