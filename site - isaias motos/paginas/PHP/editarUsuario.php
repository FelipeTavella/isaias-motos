<?php
// =========================================================
// EDITAR USUÁRIO
// Permissão: DEV, ADMIN, GERENTE
// GERENTE não pode alterar o nível
// =========================================================

session_start();
require_once("../../Conexao/conexao.php");

$nivel_logado = $_SESSION['nivel_usuario'] ?? '';

if (!in_array($nivel_logado, ['DEV', 'ADMIN', 'GERENTE'])) {
    header("Location: ../usuarios.php?erro=sem_permissao");
    exit;
}

$pode_alterar_nivel = in_array($nivel_logado, ['DEV', 'ADMIN']);
$pode_ver_senha     = $nivel_logado === 'DEV';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = intval($_POST['id'] ?? 0);
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($id <= 0) {
        header("Location: ../usuarios.php?erro=id_invalido");
        exit;
    }

    // Busca o usuário atual para manter dados intocáveis
    $stmtAtual = $conexao->prepare("SELECT * FROM usuarios WHERE codusuario = :id");
    $stmtAtual->execute([':id' => $id]);
    $atual = $stmtAtual->fetch(PDO::FETCH_ASSOC);

    if (!$atual) {
        header("Location: ../usuarios.php?erro=nao_encontrado");
        exit;
    }

    // Nível: só atualiza se tiver permissão
    if ($pode_alterar_nivel) {
        $nivel_usuario = $_POST['nivel_usuario'] ?? $atual['nivel_usuario'];
        // Bloqueia promover a DEV se não for DEV
        if ($nivel_usuario === 'DEV' && $nivel_logado !== 'DEV') {
            $nivel_usuario = $atual['nivel_usuario'];
        }
    } else {
        $nivel_usuario = $atual['nivel_usuario']; // mantém o atual
    }

    // Senha expira: só DEV pode alterar
    if ($pode_ver_senha) {
        $senha_expira_em = $_POST['senha_expira_em'] ?? $atual['senha_expira_em'];
    } else {
        $senha_expira_em = $atual['senha_expira_em'];
    }

    // Senha: só atualiza se preenchida
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET 
                    nome = :nome,
                    email = :email,
                    telefone = :telefone,
                    senha = :senha,
                    nivel_usuario = :nivel,
                    senha_expira_em = :expira
                WHERE codusuario = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':nome'     => $nome,
            ':email'    => $email,
            ':telefone' => $telefone,
            ':senha'    => $senha_hash,
            ':nivel'    => $nivel_usuario,
            ':expira'   => $senha_expira_em ?: null,
            ':id'       => $id,
        ]);
    } else {
        $sql = "UPDATE usuarios SET 
                    nome = :nome,
                    email = :email,
                    telefone = :telefone,
                    nivel_usuario = :nivel,
                    senha_expira_em = :expira
                WHERE codusuario = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':nome'     => $nome,
            ':email'    => $email,
            ':telefone' => $telefone,
            ':nivel'    => $nivel_usuario,
            ':expira'   => $senha_expira_em ?: null,
            ':id'       => $id,
        ]);
    }

    header("Location: ../usuarios.php?sucesso=editado");
    exit;
}

header("Location: ../usuarios.php");
exit;
