<?php
session_start();

// Verifica se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit();
}

$nome_cliente = htmlspecialchars($_SESSION['cliente_nome']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            Bem-vindo(a), **<?php echo $nome_cliente; ?>**!
        </div>
        <h1>Painel do Cliente</h1>
        <p>Aqui você pode gerenciar suas informações e compras.</p>
        
        <div class="d-grid gap-2 col-6 mx-auto mt-4">
            <a href="carrinho.php" class="btn btn-primary btn-lg">🛍️ Ir para o Carrinho</a>
            <a href="catalogo.php" class="btn btn-info btn-lg">🛒 Ver Produtos</a>
            <a href="logout.php" class="btn btn-danger mt-3">Sair</a>
        </div>
    </div>
</body>
</html>