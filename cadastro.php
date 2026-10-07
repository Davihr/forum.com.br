<?php
session_start();
require_once __DIR__ . '/funcoes.php';

$nome = '';
$celular = '';
$email = '';
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string) ($_POST['nome'] ?? ''));
    $celular = trim((string) ($_POST['celular'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($nome === '' || $celular === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Preencha nome, celular e um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve possuir pelo menos 6 caracteres.';
    } else {
        try {
            $usuarios = carregarXml(ARQUIVO_USUARIOS, 'usuarios');
            foreach ($usuarios->usuario as $usuario) {
                if (strcasecmp((string) $usuario->email, $email) === 0) {
                    $erro = 'Este e-mail já está cadastrado.';
                    break;
                }
            }

            if ($erro === '') {
                $novo = $usuarios->addChild('usuario');
                adicionarTextoXml($novo, 'nome', $nome);
                adicionarTextoXml($novo, 'celular', $celular);
                adicionarTextoXml($novo, 'email', $email);
                adicionarTextoXml($novo, 'senha', password_hash($senha, PASSWORD_DEFAULT));
                salvarXml($usuarios, ARQUIVO_USUARIOS);
                $sucesso = 'Usuário cadastrado com sucesso!';
                $nome = '';
                $celular = '';
                $email = '';
            }
        } catch (RuntimeException $excecao) {
            $erro = $excecao->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
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
        form { display: block; }
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
            margin: 0 auto 20px;
            max-width: 560px;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }
        .erro { background: #fdecec; color: #8b1c1c; border: 1px solid #f1b6b6; }
        .sucesso { background: #ecfdf5; color: #166534; border: 1px solid #a7f3d0; }
        .link { display: inline-block; margin-top: 12px; color: #111; text-decoration: none; font-weight: bold; }
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
        <h1>Cadastro</h1>
        <nav>
            <a href="listar.php">Fórum</a> |
            <a href="login.php">Login</a> |
            <a href="criar_topico.php">Criar tópico</a> 
        </nav>
    </header>
    <main>
        <section>
            <?php if ($erro !== ''): ?>
                <div class="alert erro"><?= escapar($erro) ?></div>
            <?php endif; ?>
            <?php if ($sucesso !== ''): ?>
                <div class="alert sucesso"><?= escapar($sucesso) ?></div>
                <p style="text-align:center; margin-top:20px;"><a class="link" href="login.php">Fazer login</a></p>
            <?php else: ?>
                <form method="post" action="cadastro.php" novalidate>
                    <label for="nome">Nome:</label>
                    <input id="nome" type="text" name="nome" value="<?= escapar($nome) ?>" placeholder="Digite seu nome" required>

                    <label for="celular">Celular:</label>
                    <input id="celular" type="tel" name="celular" value="<?= escapar($celular) ?>" placeholder="Digite seu número de celular" maxlength="15" pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" title="Use o formato: (99) 99999-9999" required>

                    <label for="email">Email:</label>
                    <input id="email" type="email" name="email" value="<?= escapar($email) ?>" placeholder="Digite seu email" required>

                    <label for="senha">Senha:</label>
                    <input id="senha" type="password" name="senha" placeholder="Digite sua senha" required>

                    <button type="submit">Cadastrar</button>
                </form>
                <p style="text-align:center; margin-top:18px;"><a class="link" href="login.php">Já possui uma conta? clique aqui!</a></p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        2026. DEV Davhcruz            <a href="https://github.com/Davihr">Conheça meu trabalho</a><p>
    </footer>
</body>
</html>
