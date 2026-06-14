<?php
// =========================================================
// ALTERAR SENHA DO PRÓPRIO USUÁRIO
// =========================================================

session_start();
require_once("../../Conexao/conexao.php");

$id = $_SESSION['codusuario'] ?? 0;
if (!$id) { header("Location: ../../index.html"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $senha_atual    = $_POST['senha_atual']    ?? '';
    $nova_senha     = $_POST['nova_senha']     ?? '';
    $confirmar      = $_POST['confirmar_senha']?? '';

    // Busca a senha atual do banco
    $stmt = $conexao->prepare("SELECT senha FROM usuarios WHERE codusuario = :id");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
        header("Location: ../meu-usuario.php?erro=senha_atual");
        exit;
    }

    if ($nova_senha !== $confirmar) {
        header("Location: ../meu-usuario.php?erro=senha_diferente");
        exit;
    }

    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $upd  = $conexao->prepare("UPDATE usuarios SET senha = :senha WHERE codusuario = :id");
    $upd->execute([':senha' => $hash, ':id' => $id]);

    header("Location: ../meu-usuario.php?sucesso=senha");
    exit;
}

header("Location: ../meu-usuario.php");
exit;
