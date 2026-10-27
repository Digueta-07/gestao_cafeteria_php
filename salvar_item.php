<?php
require_once 'funcoes.php';
exigir_login();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: painel.php');
    exit;
}
validar_csrf();

$nome = trim($_POST['nome_item'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$precoTexto = str_replace(',', '.', trim($_POST['preco'] ?? ''));
$categorias = ['Café', 'Doce', 'Salgado', 'Bebida Gelada'];

if ($nome === '' || mb_strlen($nome) > 100 ||
    !in_array($categoria, $categorias, true) ||
    !is_numeric($precoTexto) || (float)$precoTexto <= 0 || (float)$precoTexto > 99999999.99 ||
    mb_strlen($descricao) > 2000) {
    $_SESSION['mensagem'] = 'Dados inválidos. Confira os campos e tente novamente.';
    header('Location: painel.php');
    exit;
}
$preco = (float)$precoTexto;
$stmt = mysqli_prepare($conexao, 'INSERT INTO cardapio (nome_item, categoria, descricao, preco) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'sssd', $nome, $categoria, $descricao, $preco);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['mensagem'] = 'Item cadastrado com sucesso.';
header('Location: painel.php');
exit;
