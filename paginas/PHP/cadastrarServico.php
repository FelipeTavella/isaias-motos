<?php
require_once("../../Conexao/conexao.php");

$sql = "INSERT INTO servicos 
(nome, descricao, categoria, duracao_min, preco, dificuldade, tipo, status, observacoes)
VALUES
(:nome,:descricao,:categoria,:duracao,:preco,:dificuldade,:tipo,:status,:obs)";

$stmt = $conexao->prepare($sql);

$stmt->execute([
  ':nome' => $_POST['nome'],
  ':descricao' => $_POST['descricao'],
  ':categoria' => $_POST['categoria'],
  ':duracao' => $_POST['duracao_min'],
  ':preco' => $_POST['preco'],
  ':dificuldade' => $_POST['dificuldade'],
  ':tipo' => $_POST['tipo'],
  ':status' => $_POST['status'],
  ':obs' => $_POST['observacoes']
]);

header("Location: ../servicos.php");
?>