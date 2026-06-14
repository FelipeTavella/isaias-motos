<?php
require_once("../../Conexao/conexao.php");

$id = $_POST['id'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$cpf = $_POST['cpf'];
$endereco = $_POST['endereco'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];

$sql = "UPDATE clientes SET
            nome_completo = :nome,
            email = :email,
            telefone = :telefone,
            cpf = :cpf,
            endereco = :endereco,
            bairro = :bairro,
            cidade = :cidade,
            estado = :estado,
            cep = :cep
        WHERE coduser = :id";

$stmt = $conexao->prepare($sql);

$stmt->bindValue(':nome', $nome);
$stmt->bindValue(':email', $email);
$stmt->bindValue(':telefone', $telefone);
$stmt->bindValue(':cpf', $cpf);
$stmt->bindValue(':endereco', $endereco);
$stmt->bindValue(':bairro', $bairro);
$stmt->bindValue(':cidade', $cidade);
$stmt->bindValue(':estado', $estado);
$stmt->bindValue(':cep', $cep);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

$stmt->execute();

header("Location: ../clientes.php");
exit;

?>