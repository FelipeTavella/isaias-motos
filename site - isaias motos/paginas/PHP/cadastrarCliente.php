<?php
require_once("../../Conexao/conexao.php"); 

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$cpf = $_POST['cpf'];
$endereco = $_POST['endereco'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];

$sql = "INSERT INTO clientes
(nome_completo, email, telefone, cpf, endereco, bairro, cidade, estado, cep)
VALUES
(:nome, :email, :telefone, :cpf, :endereco, :bairro, :cidade, :estado, :cep)";

$stmt = $conexao->prepare($sql);

$stmt->bindParam(':nome', $nome);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':telefone', $telefone);
$stmt->bindParam(':cpf', $cpf);
$stmt->bindParam(':endereco', $endereco);
$stmt->bindParam(':bairro', $bairro);
$stmt->bindParam(':cidade', $cidade);
$stmt->bindParam(':estado', $estado);
$stmt->bindParam(':cep', $cep);

$stmt->execute();

header("Location: ../clientes.php");
exit;
?>