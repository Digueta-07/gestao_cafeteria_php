<?php
require_once 'funcoes.php';
exigir_login();
require_once 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    $_SESSION['mensagem'] = 'ID de item inválido.';
    header('Location: painel.php');
    exit;
}
$stmt = mysqli_prepare($conexao, 'SELECT id, nome_item, categoria, descricao, preco, disponivel FROM cardapio WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$item) {
    $_SESSION['mensagem'] = 'Item não encontrado.';
    header('Location: painel.php');
    exit;
}
$categorias = ['Café', 'Doce', 'Salgado', 'Bebida Gelada'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar item | Gestão Cafeteria</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topo"><div><strong>Gestão da Cafeteria</strong><span>Edição de item</span></div><div class="usuario"><a href="painel.php">Voltar ao painel</a></div></header>
<main class="container estreito">
    <section class="painel-card">
        <h1>Editar item #<?= (int)$item['id'] ?></h1>
        <p class="subtitulo">Altere os dados e salve. O item será atualizado, sem duplicação.</p>
        <form method="post" action="atualizar_item.php">
            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
            <label for="nome_item">Nome do item</label>
            <input id="nome_item" name="nome_item" maxlength="100" value="<?= escapar($item['nome_item']) ?>" required>
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
                <?php foreach ($categorias as $categoria): ?>
                <option value="<?= escapar($categoria) ?>" <?= $item['categoria'] === $categoria ? 'selected' : '' ?>><?= escapar($categoria) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4" maxlength="2000"><?= escapar($item['descricao']) ?></textarea>
            <label for="preco">Preço (R$)</label>
            <input id="preco" name="preco" type="number" min="0.01" max="99999999.99" step="0.01" value="<?= escapar(number_format((float)$item['preco'], 2, '.', '')) ?>" required>
            <label class="checkbox"><input type="checkbox" name="disponivel" value="1" <?= $item['disponivel'] ? 'checked' : '' ?>> Item disponível</label>
            <div class="botoes"><button class="botao" type="submit">Salvar alterações</button><a class="botao secundario" href="painel.php">Cancelar</a></div>
        </form>
    </section>
</main>
</body>
</html>
