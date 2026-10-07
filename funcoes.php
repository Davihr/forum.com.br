<?php

const ARQUIVO_USUARIOS = __DIR__ . DIRECTORY_SEPARATOR . 'usuarios.xml';
const ARQUIVO_TOPICOS = __DIR__ . DIRECTORY_SEPARATOR . 'topicos.xml';

function carregarXml(string $arquivo, string $raiz): SimpleXMLElement
{
    if (!is_file($arquivo)) {
        return new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $raiz . '/>');
    }

    $conteudo = file_get_contents($arquivo);
    if ($conteudo === false || trim($conteudo) === '') {
        return new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $raiz . '/>');
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($conteudo, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
    libxml_clear_errors();

    if ($xml === false || $xml->getName() !== $raiz) {
        throw new RuntimeException('O arquivo de dados está corrompido ou possui uma estrutura inválida.');
    }

    return $xml;
}

function salvarXml(SimpleXMLElement $xml, string $arquivo): void
{
    $conteudo = $xml->asXML();
    if ($conteudo === false || @file_put_contents($arquivo, $conteudo, LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível salvar os dados.');
    }
}

function adicionarTextoXml(SimpleXMLElement $pai, string $nome, string $valor): SimpleXMLElement
{
    $filho = $pai->addChild($nome, $valor);
    return $filho;
}

function escapar(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function obterIndice(mixed $valor): ?int
{
    if (!is_string($valor) && !is_int($valor)) {
        return null;
    }

    $indice = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    return $indice === false ? null : $indice;
}

function tokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfValido(mixed $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function podeExcluirComentario(mixed $comentario, string $usuarioLogado): bool
{
    if (!is_object($comentario) || !isset($comentario->autor)) {
        return false;
    }

    return strcasecmp((string) $comentario->autor, $usuarioLogado) === 0;
}

function encerrarComErro(string $mensagem, int $status = 400): never
{
    http_response_code($status);
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Erro</title>';
    echo '<style>*{box-sizing:border-box;}body{margin:0;font-family:Arial,sans-serif;background:#f4f4f4;color:#111;}';
    echo 'header{text-align:center;padding:28px 20px 10px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.08);}';
    echo 'h1{margin:0 0 15px;font-size:2rem;}nav a{color:#111;text-decoration:none;font-weight:bold;margin:0 10px;font-size:15px;}';
    echo 'main{display:flex;justify-content:center;padding:30px 15px 80px;}';
    echo 'p{max-width:560px;margin:20px auto;background:#fff;padding:25px;border-radius:12px;box-shadow:0 4px 18px rgba(0,0,0,.08);text-align:center;}';
    echo 'a{color:#111;font-weight:bold;text-decoration:none;}';
    echo 'footer{position:fixed;bottom:0;left:0;width:100%;background:rgba(255,255,255,.9);text-align:left;padding:20px 10px;font-size:14px;border-top:1px solid #e5e5e5;}</style></head><body>';
    echo '<header><h1>Erro</h1><nav><a href="listar.php">Fórum</a> | <a href="cadastro.php">Cadastro</a> | <a href="login.php">Login</a></nav></header>';
    echo '<main><p>' . escapar($mensagem) . '</p></main>';
    echo '<footer>2026. DEV Davhcruz        <a href="https://github.com/Davihr">Conheça meu trabalho</a></footer></body></html>';
    exit;
}
