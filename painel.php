<?php
require_once 'funcoes.php';
exigir_login();
require_once 'config.php';

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);

$sql = 'SELECT id, nome_item, categoria, descricao, preco, disponivel FROM cardapio ORDER BY nome_item ASC';
$resultado = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel | Gestão Cafeteria</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topo">
    <div><strong>Gestão da Cafeteria</strong><span>Painel administrativo</span></div>
    <div class="usuario">Olá, <?= escapar($_SESSION['usuario_nome'] ?? 'Gerente') ?> · <a href="logout.php">Sair</a></div>
</header>
<main class="container">
    <h1>Gerenciar cardápio</h1>
    <p class="subtitulo">Cadastre, consulte, edite ou remova produtos.</p>
    <?php if ($mensagem !== ''): ?><div class="alerta sucesso"><?= escapar($mensagem) ?></div><?php endif; ?>

    <section class="painel-card">
        <h2>Cadastrar novo item</h2>
        <form method="post" action="salvar_item.php" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
            <div>
                <label for="nome_item">Nome do item</label>
                <input id="nome_item" name="nome_item" maxlength="100" required>
            </div>
            <div>
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione</option>
                    <option value="Café">Café</option>
                    <option value="Doce">Doce</option>
                    <option value="Salgado">Salgado</option>
                    <option value="Bebida Gelada">Bebida Gelada</option>
                </select>
            </div>
            <div class="campo-largo">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3" maxlength="2000"></textarea>
            </div>
            <div>
                <label for="preco">Preço (R$)</label>
                <input id="preco" name="preco" type="number" min="0.01" max="99999999.99" step="0.01" required>
            </div>
            <div class="alinhado-fim"><button class="botao" type="submit">Cadastrar item</button></div>
        </form>
    </section>

    <section class="painel-card">
        <h2>Itens cadastrados <span class="contador"><?= mysqli_num_rows($resultado) ?></span></h2>
        <div class="tabela-responsiva">
        <table>
            <thead><tr><th>Item</th><th>Categoria</th><th>Descrição</th><th>Preço</th><th>Disponível</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($resultado) === 0): ?>
                <tr><td colspan="6" class="vazio">Nenhum item cadastrado ainda.</td></tr>
            <?php else: ?>
                <?php while ($item = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td data-label="Item"><strong><?= escapar($item['nome_item']) ?></strong></td>
                    <td data-label="Categoria"><?= escapar($item['categoria']) ?></td>
                    <td data-label="Descrição"><?= escapar($item['descricao']) ?></td>
                    <td data-label="Preço">R$ <?= number_format((float)$item['preco'], 2, ',', '.') ?></td>
                    <td data-label="Disponível"><?= $item['disponivel'] ? 'Sim' : 'Não' ?></td>
                    <td data-label="Ações" class="acoes">
                        <a class="botao pequeno secundario" href="editar_item.php?id=<?= (int)$item['id'] ?>">Editar</a>
                        <form method="post" action="excluir_item.php" onsubmit="return confirm('Tem certeza que deseja excluir este item?');">
                            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                            <button class="botao pequeno perigo" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
<footer>Gestão da Cafeteria · Sistema acadêmico de CRUD em PHP e MySQLi</footer>
</body>
</html>
