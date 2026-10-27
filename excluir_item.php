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

if (!$id || $id < 1) {
    $_SESSION['mensagem'] = 'ID de item inválido.';
    header('Location: painel.php');
    exit;
}
$stmt = mysqli_prepare($conexao, 'DELETE FROM cardapio WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$afetadas = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

$_SESSION['mensagem'] = $afetadas > 0 ? 'Item excluído com sucesso.' : 'Item não encontrado.';
header('Location: painel.php');
exit;
