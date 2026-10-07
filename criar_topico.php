<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if (!isset($_SESSION['usuario'])) {
    encerrarComErro('Você precisa estar logado para criar um tópico.', 403);
}

$titulo = '';
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf_token'] ?? null)) {
        encerrarComErro('Formulário expirado. Tente novamente.', 403);
    }

    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $mensagem = trim((string) ($_POST['mensagem'] ?? ''));

    if ($titulo === '' || $mensagem === '') {
        $erro = 'Informe o título e a mensagem.';
    } else {
        try {
            $topicos = carregarXml(ARQUIVO_TOPICOS, 'topicos');
            $novo = $topicos->addChild('topico');
            adicionarTextoXml($novo, 'autor', (string) $_SESSION['usuario']);
            adicionarTextoXml($novo, 'titulo', $titulo);
            adicionarTextoXml($novo, 'mensagem', $mensagem);
            $novo->addChild('comentarios');
            salvarXml($topicos, ARQUIVO_TOPICOS);
            $_SESSION['mensagem_sucesso'] = 'Tópico criado com sucesso!';
            header('Location: listar.php');
            exit;
        } catch (RuntimeException $excecao) {
            $erro = $excecao->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar tópico</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #111;
        }
        header {
            text-align: center;
            padding: 28px 20px 10px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }
        h1 { margin: 0 0 15px; font-size: 2rem; }
        nav a {
            color: #111;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            font-size: 15px;
        }
        main {
            display: flex;
            justify-content: center;
            padding: 30px 15px 80px;
        }
        section {
            width: min(100%, 720px);
            background: #fff;
            padding: 28px 22px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,.08);
        }
        label {
            display: block;
            font-weight: bold;
            margin: 12px 0 8px;
        }
        input, textarea, button {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #d0d0d0;
            font-size: 16px;
        }
        textarea { min-height: 150px; resize: vertical; }
        button {
            margin-top: 18px;
            background: #111;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .alert {
            background: #fdecec;
            color: #8b1c1c;
            border: 1px solid #f1b6b6;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .link { display: inline-block; margin-top: 18px; color: #111; font-weight: bold; text-decoration: none; }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(255,255,255,.9);
            text-align: left;
            padding: 14px 10px;
            font-size: 14px;
            border-top: 1px solid #e5e5e5;
        }
        footer a {
            color: #111;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Criar tópico</h1>
        <nav>
            <a href="listar.php">Fórum</a> |
            <a href="cadastro.php">Cadastro</a> |
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <section>
            <?php if ($erro !== ''): ?><div class="alert"><?= escapar($erro) ?></div><?php endif; ?>
            <form method="post" action="criar_topico.php">
                <input type="hidden" name="csrf_token" value="<?= escapar(tokenCsrf()) ?>">
                <label for="titulo">Título:</label>
                <input id="titulo" type="text" name="titulo" value="<?= escapar($titulo) ?>" required>

                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" required><?= escapar($mensagem) ?></textarea>

                <button type="submit">Criar tópico</button>
            </form>
            <p><a class="link" href="listar.php">Voltar aos tópicos</a></p>
        </section>
    </main>
    <footer>
        2026. DEV Davhcruz            <a href="https://github.com/Davihr">Conheça meu trabalho</a><p>
    </footer>
</body>
</html>
