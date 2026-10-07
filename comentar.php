<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    encerrarComErro('Método não permitido.', 405);
}

if (!isset($_SESSION['usuario'])) {
    encerrarComErro('Você precisa estar logado para comentar.', 403);
}

if (!csrfValido($_POST['csrf_token'] ?? null)) {
    encerrarComErro('Formulário expirado. Tente novamente.', 403);
}

$id = obterIndice($_POST['id'] ?? null);
$nome = trim((string) ($_POST['nome'] ?? ''));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));

if ($id === null || $mensagem === '') {
    encerrarComErro('Preencha a mensagem do comentário.');
}

$autorComentario = (string) $_SESSION['usuario'];
if ($nome !== '') {
    $autorComentario = $nome;
}

try {
    $topicos = carregarXml(ARQUIVO_TOPICOS, 'topicos');

    if (!isset($topicos->topico[$id])) {
        encerrarComErro('Tópico não encontrado.', 404);
    }

    if (!isset($topicos->topico[$id]->comentarios)) {
        $topicos->topico[$id]->addChild('comentarios');
    }

    $comentario = $topicos->topico[$id]->comentarios->addChild('comentario');
    adicionarTextoXml($comentario, 'nome', $autorComentario);
    adicionarTextoXml($comentario, 'autor', (string) $_SESSION['usuario']);
    adicionarTextoXml($comentario, 'mensagem', $mensagem);
    salvarXml($topicos, ARQUIVO_TOPICOS);

    header('Location: listar.php');
    exit;
} catch (RuntimeException $excecao) {
    encerrarComErro($excecao->getMessage(), 500);
}