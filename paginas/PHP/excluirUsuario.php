<?php
// =========================================================
// EXCLUIR USUÁRIO
// Permissão: DEV, ADMIN
// =========================================================

session_start();
require_once("../../Conexao/conexao.php");

$nivel_logado = $_SESSION['nivel_usuario'] ?? '';

if (!in_array($nivel_logado, ['DEV', 'ADMIN'])) {
    header("Location: ../usuarios.php?erro=sem_permissao");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ../usuarios.php?erro=id_invalido");
    exit;
}

// Busca o usuário alvo para verificar se é DEV
$stmtCheck = $conexao->prepare("SELECT nivel_usuario FROM usuarios WHERE codusuario = :id");
$stmtCheck->execute([':id' => $id]);
$alvo = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$alvo) {
    header("Location: ../usuarios.php?erro=nao_encontrado");
    exit;
}

// Somente DEV pode excluir outro DEV
if ($alvo['nivel_usuario'] === 'DEV' && $nivel_logado !== 'DEV') {
    header("Location: ../usuarios.php?erro=nivel_proibido");
    exit;
}

// Verifica se tem ordens_servico vinculadas
$stmtVinculo = $conexao->prepare(
    "SELECT COUNT(*) FROM ordens_servico 
     WHERE usuario_abertura = :id OR usuario_fechamento = :id"
);
$stmtVinculo->execute([':id' => $id]);
$qtd_vinculos = $stmtVinculo->fetchColumn();

if ($qtd_vinculos > 0) {
    header("Location: ../usuarios.php?erro=possui_vinculos");
    exit;
}

$stmt = $conexao->prepare("DELETE FROM usuarios WHERE codusuario = :id");
$stmt->execute([':id' => $id]);

header("Location: ../usuarios.php?sucesso=excluido");
exit;
