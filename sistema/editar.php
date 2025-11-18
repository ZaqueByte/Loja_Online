<?php 
include 'conexao.php';
$id = $_POST['btnEditar'];
$sql = $pdo->prepare("SELECT * FROM Produto WHERE id = ?");
$sql->execute([$id]);
$linha = $sql->fetch(PDO::FETCH_ASSOC);


$sql = $pdo->prepare("SELECT COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ?
  AND TABLE_NAME = ?
  AND COLUMN_NAME = ?;");

$sql->execute([$banco, 'produto', 'tipo']);
$colunaTipo = $sql->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <title>Editar Aluno</title>
</head>
<body>
    <div class="container my-4">
        <h1>Editar o produto: <?php echo $linha['nome']?></h1>
        <form action="atualizar.php" method="POST">
            <input type="hidden" name="id"
            value="<?php echo $linha['id']?>" class="form-control">

            <input type="text" name="nome" 
            value="<?php echo $linha['nome']?>" class="form-control">

            <input type="text" name="descricao"
            value="<?php echo $linha['descricao']?>" class="form-control">

            <input type="text" name="preco"
            value="<?php echo $linha['preco']?>" class="form-control">

            <?php 
                while($linha['tipo'] = $selectTipo->fetch(PDO::FETCH_ASSOC)){
            ?>
            <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                <option selected>Selecione um tipo...</option>
                <option value="1">One</option>
            </select>
            <?php } ?>

            <input type="text" name="tipo"
            value="<?php echo $linha['tipo']?>" class="form-control">
            
            <input type="text" name="categoria"
            value="<?php echo $linha['categoria']?>" class="form-control">

            <input type="date" name="data"
            value="<?php echo $linha['data_lancamento']?>" class="form-control">

            <input type="text" name="desconto"
            value="<?php echo $linha['desconto_usados']?>" class="form-control">

            <input type="submit" name="btnSalvar" value="Salvar"
            class="btn btn-primary">
        </form>
    </div>

</body>
</html>