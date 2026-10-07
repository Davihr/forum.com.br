<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$erro = '';
$mensagemSucesso = (string) ($_SESSION['mensagem_sucesso'] ?? '');
unset($_SESSION['mensagem_sucesso']);

try {
    $topicos = carregarXml(ARQUIVO_TOPICOS, 'topicos');
} catch (RuntimeException $excecao) {
    $erro = $excecao->getMessage();
    $topicos = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><topicos/>');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fórum</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #111;
        }
        a { color: inherit; }
        header {
            text-align: center;
            padding: 28px 20px 10px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }
        h1 { margin: 0 0 15px; font-size: 2rem; }
        .menu {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .menu a,
        .menu .usuario {
            color: #111;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            font-size: 15px;
        }
        main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 15px 80px;
        }
        .botao-criar {
            display: inline-block;
            margin-bottom: 20px;
            background: #111;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            padding: 12px 18px;
            font-weight: bold;
        }
        .alert {
            margin: 0 auto 20px;
            max-width: 1100px;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }
        .alert.erro { background: #fdecec; color: #8b1c1c; border: 1px solid #f1b6b6; }
        .alert.sucesso { background: #ecfdf5; color: #166534; border: 1px solid #a7f3d0; }
        article {
            background: #fff;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 22px;
            box-shadow: 0 4px 18px rgba(0,0,0,.08);
        }
        article h2 {
            margin: 0 0 10px;
            font-size: 1.7rem;
        }
        .meta {
            color: #5d524c;
            margin: 8px 0 18px;
            font-size: 0.9rem;
        }
        .mensagem {
            line-height: 1.6;
            margin: 0 0 10px;
        }
        h3 {
            margin: 28px 0 12px;
            font-size: 1.15rem;
        }
        .comentario {
            background: #fafafa;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }
        .comentario strong {
            display: block;
            margin-bottom: 4px;
        }
        .vazio {
            color: #555;
            font-style: italic;
            margin: 0;
        }
        .excluir {
            margin-top: 12px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: bold;
            cursor: pointer;
        }
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
        <div class="topo">
            <h1>Tópicos do fórum</h1>
            <nav class="menu">
                <a href="listar.php">Fórum</a> |
                <a href="cadastro.php">Cadastro</a> |
                <a href="login.php">Login</a> |
                <a href="criar_topico.php">Criar tópico</a>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <span class="usuario">Olá, <?= escapar($_SESSION['usuario']) ?></span>
                    <a href="listar.php?logout=1">Sair</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <?php if (isset($_SESSION['usuario'])): ?>
            <a class="botao-criar" href="criar_topico.php">+ Criar tópico</a>
        <?php endif; ?>

        <?php if ($mensagemSucesso !== ''): ?>
            <div class="alert sucesso"><?= escapar($mensagemSucesso) ?></div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
            <div class="alert erro"><?= escapar($erro) ?></div>
        <?php elseif (count($topicos->topico) === 0): ?>
            <p class="vazio">Nenhum tópico foi criado ainda.</p>
        <?php endif; ?>

        <?php $id = 0; ?>
        <?php foreach ($topicos->topico as $topico): ?>
            <?php $titulo = isset($topico->titulo) ? (string) $topico->titulo : ''; ?>
            <?php $mensagem = isset($topico->mensagem) ? (string) $topico->mensagem : ''; ?>
            <?php $autor = isset($topico->autor) ? (string) $topico->autor : ''; ?>

            <article>
                <h2><?= escapar($titulo) ?></h2>
                <p class="mensagem"><?= nl2br(escapar($mensagem)) ?></p>
                <p class="meta">Autor: <?= escapar($autor) ?></p>

            </article>
            <?php $id++; ?>
        <?php endforeach; ?>
    </main>
    <footer>
        2026. DEV Davhcruz            <a href="https://github.com/Davihr">Conheça meu trabalho</a><p>
    </footer>
</body>
</html>
