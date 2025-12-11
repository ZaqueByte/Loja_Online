<?php 
    include 'conexao.php';
    $sql = $pdo->query("SELECT * FROM Produto");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <title>Gerenciamento de Produtos</title>
</head>

<body>

    <div class="container my-5">
        <h1>Produtos Cadastrados</h1>
        <a href="index.php" class="btn btn-secondary mb-4">Voltar ao Painel</a>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Preço</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Lançamento</th>
                    <th scope="col">Desconto (%)</th>
                    <th scope="col" colspan="2">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                while($linha = $sql->fetch(PDO::FETCH_ASSOC)){
            ?>
                <tr>
                    <th scope="row"><?php echo htmlspecialchars($linha['id'])?></th>
                    <td><?php echo htmlspecialchars($linha['nome'])?></td>
                    <td><?php echo htmlspecialchars(substr($linha['descricao'], 0, 50)) . '...' ?></td>
                    <td>R$ <?php echo number_format($linha['preco'], 2, ',', '.') ?></td>
                    <td><?php echo htmlspecialchars($linha['tipo']) ?></td>
                    <td><?php echo htmlspecialchars($linha['categoria']) ?></td>
                    <td><?php 
                        $partes = explode('-', $linha['data_lancamento']);
                        $data = "".$partes[2]."/".$partes[1]."/".$partes[0];
                        echo $data ?>
                    </td>
                    <td><?php echo htmlspecialchars($linha['desconto_usados']) ?></td>
                    <td><form action="editar.php" method="POST">
                        <button class="btn btn-sm btn-primary" name="btnEditar" 
                        value="<?php echo $linha['id'];?>">Editar</button>
                    </form></td>

                    <td><form action="excluir.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');"> 
                        <button class="btn btn-sm btn-danger" name="btnExcluir" 
                        value="<?php echo $linha['id'];?>">Excluir</button>
                    </form></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        
        <h2 class="mt-5 mb-3">Adicionar Novo Produto</h2>
        <form action="adicionar.php" method="POST" class="row g-3 p-3 border rounded shadow-sm">
            <div class="col-md-6">
                <label class="form-label">Nome do Produto</label>
                <input type="text" name="txtNome" class="form-control"
                placeholder="Nome" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descrição</label>
                <input type="text" name="txtDescricao" class="form-control"
                placeholder="Descrição breve" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" name="txtPreco" class="form-control"
                placeholder="99.99" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <input type="text" name="txtTipo" class="form-control"
                placeholder="Novo ou Usado" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoria</label>
                <input type="text" name="txtCategoria" class="form-control"
                placeholder="Ex: Informática" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Lançamento</label>
                <input type="date" name="txtData" class="form-control"
                required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Desconto Usados (%)</label>
                <input type="number" step="0.01" name="txtDesconto" class="form-control"
                placeholder="0.00" value="0.00">
            </div>

            <div class="col-12 mt-4">
                <input type="submit" value="Salvar Produto" name="btnSalvar" class="btn btn-success">
            </div>
        </form>
    </div>
</body>

</html>