<?php
// =========================================================
// CADASTRAR USUÁRIO
// Permissão: DEV, ADMIN
// =========================================================

session_start();
require_once("../../Conexao/conexao.php");

$nivel_logado = $_SESSION['nivel_usuario'] ?? '';

if (!in_array($nivel_logado, ['DEV', 'ADMIN'])) {
    header("Location: ../usuarios.php?erro=sem_permissao");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome            = trim($_POST['nome'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $telefone        = trim($_POST['telefone'] ?? '');
    $senha           = $_POST['senha'] ?? '';
    $nivel_usuario   = $_POST['nivel_usuario'] ?? 'ATENDENTE';
    $senha_expira_em = $_POST['senha_expira_em'] ?? null;

    // Somente DEV pode definir nível DEV
    if ($nivel_usuario === 'DEV' && $nivel_logado !== 'DEV') {
        header("Location: ../usuarios.php?erro=nivel_proibido");
        exit;
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios 
              (nome, email, senha, telefone, nivel_usuario, senha_expira_em, conta_criada)
            VALUES 
              (:nome, :email, :senha, :telefone, :nivel, :expira, NOW())";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':senha'    => $senha_hash,
        ':telefone' => $telefone,
        ':nivel'    => $nivel_usuario,
        ':expira'   => $senha_expira_em ?: null,
    ]);

    header("Location: ../usuarios.php?sucesso=cadastrado");
    exit;
}

header("Location: ../usuarios.php");
exit;
