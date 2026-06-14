<?php
// =========================================================
// EDITAR PRÓPRIO PERFIL
// =========================================================

session_start();
require_once("../../Conexao/conexao.php");

$id = $_SESSION['codusuario'] ?? 0;
if (!$id) { header("Location: ../../index.html"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim($_POST['nome']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    $stmt = $conexao->prepare(
        "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone
         WHERE codusuario = :id"
    );
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':telefone' => $telefone,
        ':id'       => $id,
    ]);

    header("Location: ../meu-usuario.php?sucesso=perfil");
    exit;
}

header("Location: ../meu-usuario.php");
exit;
