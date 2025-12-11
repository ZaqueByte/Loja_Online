<?php
session_start();
include 'conexao.php';

// Permite visualizar mesmo sem login, mas o botão de compra só funciona se houver login
$logado = isset($_SESSION['cliente_id']);
$id_loja = 1; // Fixo na loja 1 para simplificar o link com estoque

$sql = $pdo->prepare("
    SELECT 
        p.id AS idprod,
        p.nome, p.descricao, p.preco, p.tipo, p.categoria, p.desconto_usados,
        (p.preco * (1 - p.desconto_usados / 100)) AS preco_final,
        e.quantidade_disponivel, l.cidade
    FROM Produto p
    JOIN Estoque e ON p.id = e.id_produto
    JOIN Loja l ON e.id_loja = l.id
    WHERE e.id_loja = ? AND e.quantidade_disponivel > 0
    ORDER BY p.data_lancamento DESC
");
$sql->execute([$id_loja]);
$produtos = $sql->fetchAll(PDO::FETCH_ASSOC);

$nome_loja = "Amazon(as) - " . ($produtos[0]['cidade'] ?? 'N/A');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Catálogo de Produtos - <?php echo htmlspecialchars($nome_loja); ?></h1>
            <a href="carrinho.php" class="btn btn-warning">
                🛒 Ver Carrinho
                <?php if ($logado): ?>
                    (<?php 
                        // Exibe o total de itens no carrinho (simplificado)
                        $stmt_count = $pdo->prepare("SELECT SUM(quantidade) FROM CarrinhoTemporario WHERE id_cliente = ?");
                        $stmt_count->execute([$_SESSION['cliente_id']]);
                        echo $stmt_count->fetchColumn() ?: 0;
                    ?>)
                <?php endif; ?>
            </a>
            <a href="<?php echo $logado ? 'painel_cliente.php' : 'login.php'; ?>" class="btn btn-secondary">
                <?php echo $logado ? 'Voltar ao Painel' : 'Fazer Login'; ?>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($produtos as $produto): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                            <p class="mb-1">
                                Categoria: <strong><?php echo htmlspecialchars($produto['categoria']); ?></strong>
                            </p>
                            
                            <?php if ($produto['desconto_usados'] > 0): ?>
                                <p class="text-decoration-line-through text-danger mb-0">De: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                <p class="fs-4 fw-bold text-success">Por: R$ <?php echo number_format($produto['preco_final'], 2, ',', '.'); ?></p>
                            <?php else: ?>
                                <p class="fs-4 fw-bold">Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            <?php endif; ?>

                            <p class="text-sm mt-auto mb-2">Em estoque: <?php echo htmlspecialchars($produto['quantidade_disponivel']); ?></p>
                            
                            <form action="carrinho.php" method="POST">
                                <input type="hidden" name="id_produto_adicionar" value="<?php echo $produto['idprod']; ?>">
                                <?php if ($logado): ?>
                                    <button type="submit" class="btn btn-primary w-100">Adicionar ao Carrinho</button>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-outline-primary w-100 disabled" tabindex="-1" role="button" aria-disabled="true">Faça Login para Comprar</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($produtos)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">Nenhum produto em estoque nesta loja no momento.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>