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

    var_dump($_SESSION);
    Tweet::create([
        'username' => $loggedInUser,
        'content' => $content,
        'user_id' => $_SESSION['user_id']
    ]);

    // Redireciona para evitar reenvio do formulário
    header('Location: feed.php');
    exit();
}

// Processa a exclusão de um tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tweet'])) {
    $tweetId = $_POST['tweet_id'];
    $tweet = Tweet::find($tweetId);
    $tweet->delete();

    // Redireciona para evitar reenvio do formulário
    header('Location: feed.php');
    exit();
}

// Processa a edição de um tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_tweet'])) {
    $tweetId = $_POST['tweet_id'];
    $newContent = $_POST['tweet_content'];
    $tweet = Tweet::find($tweetId);

    if ($tweet && $tweet->user_id === $_SESSION['user_id']) {
        $tweet->content = $newContent;
        $tweet->is_edited = true;
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

// Processa o "like" em um tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_tweet'])) {
    $tweetId = $_POST['tweet_id'];
    $userId = User::where('username', $loggedInUser)->value('id');

    $existingLike = $capsule->table('likes')->where('tweet_id', $tweetId)->where('user_id', $userId)->first();

    if ($existingLike) {
        // Descurtir se já estiver curtido
        $capsule->table('likes')->where('tweet_id', $tweetId)->where('user_id', $userId)->delete();
    } else {
        // Curtir se ainda não estiver curtido
        $capsule->table('likes')->insert([
            'tweet_id' => $tweetId,
            'user_id' => $userId,
        ]);
    }

    // Redireciona para evitar reenvio do formulário
    header('Location: feed.php');
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
            background-color: #89d7d4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* container principal */
        .container {
            margin-top: 65px;
            margin-left: auto;
            margin-right: auto;
            background-color: #FDFCF7;
            max-width: 600px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* botão de logout 
        .logout-button {
            background-color: #E0245E;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }*/

        .logout-button {
            background-color: #E0245E;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        /* botão do nome de usuário */
        .username-badge {
            background-color: #0ea8e9;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
        
        .username-badge:hover {
            color: white;
            transform: scale(1.1);
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
            resize: none;
            margin-top: 5px;
        }

        .tweet-form button {
            background-color: #0ea8e9;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        /* container de tweets */
        .tweet-container {
            width: 100%;
            border-top: 1px solid #e1e8ed;
            padding-top: 20px;
        }

        .tweet {
            width: 100%;
            border-bottom: 1px solid #e1e8ed;
            padding: 10px 0;
        }

        .tweet p {
            margin: 0;
            word-wrap: break-word;
            word-break: break-all;
        }

        .tweet small {
            color: #657786;
        }

        .tweet-actions {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tweet-actions button,
        .tweet-actions a {
            background-color: #0ea8e9;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
                
        .tweet-actions button.delete {
            background-color: #E0245E;
        }

        .tweet-actions button.like {
            background-color: #0ea8e9;
        }

        .tweet-actions button.like.active {
            background-color: #E0245E;
        }

        .tweet-actions span {
            font-size: 14px;
            color: #657786;
        }

    /* Estilos do menu geral */
    #menu-geral {
            display: flex;
            background-color: #FDFCF7;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(0, 0, 0, 0.299);
            height: 64px;
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            z-index: 1000;
            box-shadow: 3px 1px 8px rgba(0, 0, 0, 0.25);
        }

        #logo {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        #menu {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }

        #menu ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        #menu ul li {
            display: inline;
            padding: 20px;
        }
        /*
        #menu ul li button {
            margin-right: 200%;
        }*/

        #menu ul li button:hover {
            background-color: red;
            color: white;
            transform: scale(1.1);
        }
        #deslogar {
            background-color: #E0245E;
            padding: 5px 10px;

            
        }
        #textedit{
            resize: none;
        }
        .edicao{
            
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        #edsalva{
            background-color: green;
            color: #fff; 
            border: none; 
            padding: 5px 9px; 
            cursor: pointer; 
        }
        #edvolta{
            background-color: #E0245E;
        }


    </style>
</head>

<body>
    <div id="menu-geral">
        <div id="menu">
            <ul>
                <li>
                    <form action="profile.php" method="GET" style="display: inline;">
                        <div class="username-badge" onclick="this.closest('form').submit();">
                            @<?php echo htmlspecialchars($loggedInUser); ?>
                        </div>
                    </form>
                </li>
                <li>
                    <a href="feed.php">
                        <img src="TRENDY.png" alt="Trendy logo" id="logo">
                    </a>
                </li>
                <li>
                    <form action="feed.php" method="POST" style="display: inline;">
                        <button class="username-badge" id = "deslogar" type="submit" name="logout">Deslogar</button>
                    </form>
                </li>
                <?php if ($_SESSION['cargo'] == 'admin') : ?>
                    <style>
                        #menu-geral {
                        background-color:#E0245E;
                    }
                    </style>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <!-- Formulário para novo tweet -->
        <div class="tweet-form">
            <form action="feed.php" method="POST">
                <textarea placeholder="O que está acontecendo?" name="tweet" maxlength="280" rows="4" required></textarea>
                <button type="submit">Trendar</button>
            </form>
        </div>
        <!-- Exibição dos tweets -->
        <div class="tweet-container">
            <?php
            foreach ($tweets as $tweet) :
                $user = User::find($tweet->user_id);
                $likeCount = $capsule->table('likes')->where('tweet_id', $tweet->id)->count();
                $userId = User::where('username', $loggedInUser)->value('id');
                $userLiked = $capsule->table('likes')->where('tweet_id', $tweet->id)->where('user_id', $userId)->exists();
            ?>
                <div class="tweet">
                    <p><strong>@<?php echo htmlspecialchars($user->username); ?></strong></p>
                    <p><?php echo nl2br(htmlspecialchars($tweet->content)); ?></p>
                    <small><?php echo htmlspecialchars($tweet->created_at); ?><?php echo $tweet->is_edited ? ' (editado)' : ''; ?></small>

                    <div class="tweet-actions">
                        <!-- Botão para curtir/descurtir o tweet -->
                        <form action="feed.php" method="POST" style="display:inline;">
                            <input type="hidden" name="tweet_id" value="<?php echo $tweet->id; ?>">
                            <button type="submit" name="like_tweet" class="like <?php echo $userLiked ? 'active' : ''; ?>">
                                <?php echo $userLiked ? 'Descurtir' : 'Curtir'; ?>
                            </button>
                            <span><?php echo $likeCount; ?> curtidas</span>
                        </form>

                        <!-- Botão para editar o tweet (exibido somente se o usuário logado for o autor) -->
                        <?php if ($tweet->user_id == $_SESSION['user_id']) : ?>
                            <a href="feed.php?edit=<?php echo $tweet->id; ?>">Editar</a>
                        <?php endif; ?>    

                        <?php if ($tweet->user_id == $_SESSION['user_id'] || $_SESSION['cargo']=='admin') : ?>
                            <!-- Formulário para deletar o tweet -->
                            <form action="feed.php" method="POST" style="display:inline;">
                                <input type="hidden" name="tweet_id" value="<?php echo $tweet->id; ?>">
                                <button type="submit" name="delete_tweet" class="delete">Deletar</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Formulário de edição (exibido somente se o usuário logado for o autor) -->
                    <?php if (isset($_GET['edit']) && $_GET['edit'] == $tweet->id && $tweet->user_id === $_SESSION['user_id']) : ?>
                        <form action="feed.php" method="POST">
                            <input type="hidden" name="tweet_id" value="<?php echo $tweet->id; ?>">
                            <textarea id="textedit" name="tweet_content" rows="4" maxlength="280" required><?php echo htmlspecialchars($tweet->content); ?></textarea>
                            <button type="submit" name="edit_tweet" class="edicao" id="edsalva">Salvar</button>
                            <a href="feed.php" class="edicao" id="edvolta">Voltar</a>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
