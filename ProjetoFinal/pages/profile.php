<?php
require __DIR__ . '/../database/database.php';
require __DIR__ . '/../models/User.php';

// Inicie a sessão
session_start();

// Verifique se o usuário está autenticado e se a sessão contém o nome de usuário
if (!isset($_SESSION['username'])) {
    die('Você precisa estar logado para acessar esta página.');
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Nome de usuário do usuário logado
$loggedInUser = $_SESSION['username'];
$user = User::find($_SESSION['user_id']);

// Processa a atualização das informações do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword === $confirmPassword) {
            $user->password = password_hash($newPassword, PASSWORD_BCRYPT); // Atualiza a senha
        } else ($error = 'Senhas não coincidem');
    }


    $existingUser = User::where('username', $newUsername)->first();

    if (isset($newUsername) && !empty($newUsername)) {
        if (!$existingUser || $existingUser->id == $_SESSION['user_id']) {
            $user->username = $newUsername; // Atualiza o nome de usuário
            $user->save();
            $_SESSION['username'] = $newUsername; // Atualiza o username da sessão
        } else {
            $error = 'Username já está em uso';
        }
    } else {
        $error = 'As senhas não coincidem.';
    }
    if (!isset($error)) {
        header('Location: feed.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<style>
    body{
        background-color: #89d7d4;
    }
    .register-container{
        background-color: #FDFCF7;
    }
    .vol {
        background-color: #E0245E;
        color: #fff; 
        border: none; 
        padding: 10px 20px; 
        cursor: pointer; 
    }

    .vol:hover {
        background-color: #E0245E; 
        color: #fff; 
    }
</style>
<body>
    <div class="login-container">
        <a>
            <img src="assets/TRENDY.png" alt="Logo trendy" id="logo">
        </a>
        <?php if (isset($error)) : ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Formulário para atualizar o perfil -->
        <form action="profile.php" method="POST">
            <div class="form-group">
                <label for="new_username">Novo Nome de Usuário:</label>
                <input type="text" name="new_username" id="new_username" value="<?php echo htmlspecialchars($user->username); ?>">
            </div>

            <div class="form-group">
                <label for="new_password">Nova Senha:</label>
                <input type="password" name="new_password" id="new_password">
            </div>

            <div class="form-group">

                <label for="confirm_password">Confirme a Nova Senha:</label>
                <input type="password" name="confirm_password" id="confirm_password">
            </div>


            <button type="submit">Atualizar</button>

        </form>
        <br>
        <a href="feed.php"><button class="vol" id="vol">Voltar</button></a>        
    </div>
</body>

</html>