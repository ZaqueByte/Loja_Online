<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$senha_digitada = $_POST['senha'];

// 1. Validação no Backend
if (empty($email) || empty($senha_digitada) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_erro'] = true;
    header("Location: login.php");
    exit();
}

try {
    // 2. Usar prepared statement para buscar cliente
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash FROM Cliente WHERE email = ?");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verificar a senha com password_verify()
    if ($cliente && password_verify($senha_digitada, $cliente['senha_hash'])) {
        
        // Login bem-sucedido: inicia a sessão
        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        
        // Redireciona para o painel do cliente
        header("Location: painel_cliente.php"); 
        exit();
        
    } else {
        // Falha: redireciona com erro
        $_SESSION['login_erro'] = true;
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    // Log do erro real e mensagem genérica para o usuário
    error_log("Erro de autenticação: " . $e->getMessage());
    $_SESSION['login_erro'] = true;
    header("Location: login.php");
    exit();
}
?>