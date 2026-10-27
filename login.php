<?php
require_once 'funcoes.php';
require_once 'config.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: painel.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {
        $erro = 'Informe o usuário e a senha.';
    } else {
        $sql = 'SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ? LIMIT 1';
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, 's', $usuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $dados = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);

       
        $senhaValida = false;
        if ($dados) {
            if (password_verify($senha, $dados['senha'])) {
                $senhaValida = true;
            } elseif (hash_equals((string)$dados['senha'], (string)$senha)) {
                $senhaValida = true;
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $upd = mysqli_prepare($conexao, 'UPDATE usuarios SET senha = ? WHERE id = ?');
                mysqli_stmt_bind_param($upd, 'si', $hash, $dados['id']);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
            }
        }

        if ($senhaValida) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int)$dados['id'];
            $_SESSION['usuario_nome'] = $dados['nome'];
            header('Location: painel.php');
            exit;
        }
        $erro = 'Usuário ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Gestão Cafeteria</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<main class="login-card">
    <h1>Gestão da Cafeteria</h1>
    <p class="subtitulo">Acesse o painel administrativo</p>
    <?php if ($erro !== ''): ?><div class="alerta erro"><?= escapar($erro) ?></div><?php endif; ?>
    <form method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
        <label for="usuario">Usuário</label>
        <input id="usuario" name="usuario" type="text" maxlength="50" required autocomplete="username">
        <label for="senha">Senha</label>
        <input id="senha" name="senha" type="password" required autocomplete="current-password">
        <button type="submit" class="botao">Entrar</button>
    </form>
    <p class="dica">Acesso inicial: <strong>admin</strong> / <strong>admin123</strong></p>
</main>
</body>
</html>
