<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if (isset($_SESSION['usuario'])) {
    header('Location: listar.php');
    exit;
}

$email = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha = (string) ($_POST['senha'] ?? '');

    try {
        $usuarios = carregarXml(ARQUIVO_USUARIOS, 'usuarios');
        foreach ($usuarios->usuario as $usuario) {
            $hash = (string) $usuario->senha;
            $senhaCorreta = password_verify($senha, $hash)
                || (preg_match('/^[a-f0-9]{32}$/i', $hash) === 1 && hash_equals(strtolower($hash), md5($senha)));

            if (strcasecmp((string) $usuario->email, $email) === 0 && $senhaCorreta) {
                session_regenerate_id(true);
                $_SESSION['usuario'] = (string) $usuario->email;

                if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                    $usuario->senha = password_hash($senha, PASSWORD_DEFAULT);
                    salvarXml($usuarios, ARQUIVO_USUARIOS);
                }

                header('Location: listar.php');
                exit;
            }
        }

        $erro = 'Login inválido.';
    } catch (RuntimeException $excecao) {
        $erro = $excecao->getMessage();
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            width: min(100%, 560px);
            background: #fff;
            padding: 28px 22px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,.08);
        }
        label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin: 10px 0 8px;
        }
        input, button {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #d0d0d0;
            font-size: 16px;
        }
        input:focus { outline: none; border-color: #111; }
        button {
            margin-top: 18px;
            background: #111;
            border: none;
            color: #fff;
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
            text-align: center;
            margin-bottom: 18px;
        }
        .link { display: inline-block; margin-top: 18px; color: #111; text-decoration: none; font-weight: bold; }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(255,255,255,.9);
            text-align: left;
            padding: 7px 5px;
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
        <h1>Login</h1>
        <nav>
            <a href="listar.php">Fórum</a> |
            <a href="cadastro.php">Cadastro</a> |
            <a href="criar_topico.php">Criar tópico</a>
        </nav>
    </header>
    <main>
        <section>
            <?php if ($erro !== ''): ?><div class="alert"><?= escapar($erro) ?></div><?php endif; ?>
            <form method="post" action="login.php" novalidate>
                <label for="email">E-mail:</label>
                <input id="email" type="email" name="email" value="<?= escapar($email) ?>" placeholder="Digite seu e-mail" required>

                <label for="senha">Senha:</label>
                <input id="senha" type="password" name="senha" placeholder="Digite sua senha" required>

                <button type="submit">Entrar</button>
            </form>
            <p style="text-align:center;"><a class="link" href="cadastro.php">Criar cadastro</a></p>
        </section>
    </main>
    <footer>
        <p>2026. DEV Davhcruz            <a href="https://github.com/Davihr">Conheça meu trabalho</a><p>
    </footer>
</body>
</html>
