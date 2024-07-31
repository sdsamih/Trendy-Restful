<?php
require 'database.php';
require 'User.php';
require 'Tweet.php';

// Inicie a sessão
session_start();

// Verifique se o usuário está autenticado e se a sessão contém o nome de usuário
if (!isset($_SESSION['username'])) {
    die('Você precisa estar logado para acessar esta página.');
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Recupera todos os tweets, ordenados por data
$tweets = Tweet::orderBy('created_at', 'desc')->get();

// Nome de usuário do usuário logado
$loggedInUser = $_SESSION['username'];

// Processa o envio de um novo tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tweet'])) {
    $content = $_POST['tweet'];

    Tweet::create([
        'username' => $loggedInUser,
        'content' => $content,
    ]);

    // Redireciona para evitar reenvio do formulário
    header('Location: feed.php');
    exit();
}

// Processa a exclusão de um tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tweet'])) {
    $tweetId = $_POST['tweet_id'];
    $tweet = Tweet::find($tweetId);

    if ($tweet && $tweet->username === $loggedInUser) {
        $tweet->delete();
    }

    // Redireciona para evitar reenvio do formulário
    header('Location: feed.php');
    exit();
}

// Processa a edição de um tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_tweet'])) {
    $tweetId = $_POST['tweet_id'];
    $newContent = $_POST['tweet_content'];
    $tweet = Tweet::find($tweetId);

    if ($tweet && $tweet->username === $loggedInUser) {
        $tweet->content = $newContent;
        $tweet->save();
    }

    // Redireciona para evitar reenvio do formulário
    header('Location: feed.php');
    exit();
}

// Processa o logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php'); // Redireciona para a página de login após o logout
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed de Tweets</title>
    <style>
        /* estilos gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* container principal */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* container de botões flutuantes */
        .floating-buttons {
            position: fixed;
            top: 10px;
            left: 10px;
        }

        /* botão de logout */
        .logout-button {
            background-color: #E0245E; /* cor para o botão de logout */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px; /* Espaço acima do botão */
        }

        /* botão do nome de usuário */
        .username-badge {
            background-color: #1DA1F2; /* cor do Twitter */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            cursor: pointer;
        }

        /* formulário para novo tweet */
        .tweet-form {
            margin-bottom: 20px;
        }

        .tweet-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .tweet-form button {
            background-color: #1DA1F2; /* cor do Twitter */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        /* container de tweets */
        .tweet-container {
            border-top: 1px solid #e1e8ed;
            padding-top: 20px;
        }

        .tweet {
            border-bottom: 1px solid #e1e8ed;
            padding: 10px 0;
        }

        .tweet p {
            margin: 0;
        }

        .tweet small {
            color: #657786;
        }

        .tweet-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Container de botões flutuantes -->
    <div class="floating-buttons">
        <!-- Nome de usuário flutuante -->
        <form action="profile.php" method="POST" style="display: inline;">
            <div class="username-badge" onclick="this.closest('form').submit();">
                @<?php echo htmlspecialchars($loggedInUser); ?>
            </div>
        </form>

        <!-- Botão de logout -->
        <form action="feed.php" method="POST" style="display: inline;">
            <button class="logout-button" type="submit" name="logout">Deslogar</button>
        </form>
    </div>

    <div class="container">
        <!-- Formulário para novo tweet -->
        <div class="tweet-form">
            <form action="feed.php" method="POST">
                <textarea name="tweet" placeholder="O que está acontecendo?" rows="4" required></textarea>
                <button type="submit">Tweetar</button>
            </form>
        </div>

        <!-- Exibição dos tweets -->
        <div class="tweet-container">
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <p><strong>@<?php echo htmlspecialchars($tweet->username); ?></strong></p>
                    <p><?php echo nl2br(htmlspecialchars($tweet->content)); ?></p>
                    <small><?php echo htmlspecialchars($tweet->created_at); ?></small>

                    <?php if ($tweet->username === $loggedInUser): ?>
                        <div class="tweet-actions">
                            <!-- Formulário para apagar o tweet -->
                            <form action="feed.php" method="POST" style="display:inline;">
                                <input type="hidden" name="tweet_id" value="<?php echo $tweet->id; ?>">
                                <button type="submit" name="delete_tweet" style="background-color: #E0245E; border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Apagar</button>
                            </form>

                            <!-- Formulário para editar o tweet -->
                            <form action="feed.php" method="POST" style="display:inline;">
                                <input type="hidden" name="tweet_id" value="<?php echo $tweet->id; ?>">
                                <input type="text" name="tweet_content" value="<?php echo htmlspecialchars($tweet->content); ?>" required>
                                <button type="submit" name="edit_tweet" style="background-color: #1DA1F2; border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Editar</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
