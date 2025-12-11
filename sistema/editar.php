<?php 
include 'conexao.php';
// Verifica se o ID foi passado via POST (botão Editar)
if (!isset($_POST['btnEditar'])) {
    header("Location: produtos.php");
    exit;
}

$id = $_POST['btnEditar'];
$sql = $pdo->prepare("SELECT * FROM Produto WHERE id = ?");
$sql->execute([$id]);
$linha = $sql->fetch(PDO::FETCH_ASSOC);

if (!$linha) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Produto não encontrado.</div><a href='produtos.php'>Voltar</a></div>";
    exit;
}

// Código PHP desnecessário/incorreto removido.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <title>Editar Produto</title>
</head>
<body>
    <div class="container my-4">
        <h1>Editar o produto: <?php echo htmlspecialchars($linha['nome'])?></h1>
        <form action="atualizar.php" method="POST" class="row g-3 p-3 border rounded shadow-sm">
            <input type="hidden" name="id"
            value="<?php echo htmlspecialchars($linha['id'])?>" class="form-control">

            <div class="col-md-6">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" 
                value="<?php echo htmlspecialchars($linha['nome'])?>" class="form-control" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Descrição</label>
                <input type="text" name="descricao"
                value="<?php echo htmlspecialchars($linha['descricao'])?>" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Preço</label>
                <input type="number" step="0.01" name="preco"
                value="<?php echo htmlspecialchars($linha['preco'])?>" class="form-control" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <input type="text" name="tipo"
                value="<?php echo htmlspecialchars($linha['tipo'])?>" class="form-control">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Categoria</label>
                <input type="text" name="categoria"
                value="<?php echo htmlspecialchars($linha['categoria'])?>" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Data de Lançamento</label>
                <input type="date" name="data"
                value="<?php echo htmlspecialchars($linha['data_lancamento'])?>" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Desconto Usados (%)</label>
                <input type="number" step="0.01" name="desconto"
                value="<?php echo htmlspecialchars($linha['desconto_usados'])?>" class="form-control">
            </div>

            <div class="col-12 mt-4">
                <input type="submit" name="btnSalvar" value="Salvar Alterações"
                class="btn btn-success">
                <a href="produtos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</body>
</html>