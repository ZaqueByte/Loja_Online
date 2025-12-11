<?php
session_start();
include 'conexao.php';

// Redireciona se não estiver logado ou se não veio do POST (segurança)
if (!isset($_SESSION['cliente_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['cliente_id'];
$id_loja = 1; // Assumindo Loja 1 para simplificar o projeto
$valor_total_compra = $_POST['valor_total'] ?? 0;
$mensagem = "";
$sucesso = false;

try {
    $pdo->beginTransaction(); // Inicia a transação

    // 1. Consulta itens do carrinho para processamento
    $sql_carrinho = $pdo->prepare("
        SELECT 
            ct.id_produto, ct.quantidade,
            (p.preco * (1 - p.desconto_usados / 100)) AS preco_unitario_final,
            e.quantidade_disponivel
        FROM CarrinhoTemporario ct
        JOIN Produto p ON ct.id_produto = p.id
        JOIN Estoque e ON p.id = e.id_produto AND e.id_loja = ?
        WHERE ct.id_cliente = ?
    ");
    $sql_carrinho->execute([$id_loja, $id_cliente]);
    $itens_carrinho = $sql_carrinho->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens_carrinho) || $valor_total_compra <= 0) {
        throw new Exception("O carrinho está vazio ou o valor total é inválido.");
    }

    // 2. Verifica o estoque para cada item (Verificação de Estoque Antes da Finalização)
    foreach ($itens_carrinho as $item) {
        if ($item['quantidade'] > $item['quantidade_disponivel']) {
            throw new Exception("Estoque insuficiente para o produto ID: " . $item['id_produto'] . " (Apenas " . $item['quantidade_disponivel'] . " em estoque).");
        }
    }

    // 3. Insere na tabela VENDA
    $stmt_venda = $pdo->prepare("
        INSERT INTO Venda (id_cliente, id_loja, valor_total) 
        VALUES (?, ?, ?)
    ");
    $stmt_venda->execute([$id_cliente, $id_loja, $valor_total_compra]);
    $id_venda = $pdo->lastInsertId();

    // 4. Itera e insere em ITEMVENDA e atualiza ESTOQUE
    $stmt_item = $pdo->prepare("
        INSERT INTO ItemVenda (id_venda, id_produto, quantidade, preco_unitario) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt_estoque = $pdo->prepare("
        UPDATE Estoque SET quantidade_disponivel = quantidade_disponivel - ?
        WHERE id_produto = ? AND id_loja = ?
    ");

    foreach ($itens_carrinho as $item) {
        // Insere ItemVenda (Preço fixado no momento da venda)
        $stmt_item->execute([
            $id_venda, 
            $item['id_produto'], 
            $item['quantidade'], 
            $item['preco_unitario_final']
        ]);
        
        // Atualiza Estoque (reduzindo a quantidade)
        $stmt_estoque->execute([
            $item['quantidade'], 
            $item['id_produto'], 
            $id_loja
        ]);
    }

    // 5. Limpa o carrinho temporário
    $stmt_limpa = $pdo->prepare("DELETE FROM CarrinhoTemporario WHERE id_cliente = ?");
    $stmt_limpa->execute([$id_cliente]);

    $pdo->commit(); // Confirma a transação
    $mensagem = "<div class='alert alert-success'>Compra finalizada com sucesso! Número do Pedido: **#$id_venda**</div>";
    $sucesso = true;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Desfaz a transação em caso de erro
    }
    $mensagem = "<div class='alert alert-danger'>Erro ao finalizar a compra: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Compra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1>Status da Compra</h1>
        <?php echo $mensagem; ?>
        <a href="painel_cliente.php" class="btn btn-primary mt-3">Voltar ao Painel</a>
        <?php if ($sucesso) : ?>
            <a href="carrinho.php" class="btn btn-secondary mt-3">Ver meus Pedidos (a implementar)</a>
        <?php endif; ?>
    </div>
</body>
</html>