<?php
require_once 'funcoes.php';
exigir_login();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: painel.php');
    exit;
}
validar_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome_item'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$precoTexto = str_replace(',', '.', trim($_POST['preco'] ?? ''));
$disponivel = isset($_POST['disponivel']) ? 1 : 0;
$categorias = ['Café', 'Doce', 'Salgado', 'Bebida Gelada'];

if (!$id || $id < 1 || $nome === '' || mb_strlen($nome) > 100 ||
    !in_array($categoria, $categorias, true) ||
    !is_numeric($precoTexto) || (float)$precoTexto <= 0 || (float)$precoTexto > 99999999.99 ||
    mb_strlen($descricao) > 2000) {
    $_SESSION['mensagem'] = 'Dados inválidos. Confira os campos e tente novamente.';
    header('Location: painel.php');
    exit;
}
$preco = (float)$precoTexto;
$stmt = mysqli_prepare($conexao, 'UPDATE cardapio SET nome_item = ?, categoria = ?, descricao = ?, preco = ?, disponivel = ? WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'sssdii', $nome, $categoria, $descricao, $preco, $disponivel, $id);
mysqli_stmt_execute($stmt);
$afetadas = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

$_SESSION['mensagem'] = 'Alterações salvas com sucesso.';
header('Location: painel.php');
exit;
