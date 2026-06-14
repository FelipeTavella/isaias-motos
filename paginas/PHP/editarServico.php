<?php
require_once("../../Conexao/conexao.php");

$sql = "UPDATE servicos SET
nome=:nome,
descricao=:descricao,
categoria=:categoria,
duracao_min=:duracao,
preco=:preco,
dificuldade=:dificuldade,
tipo=:tipo,
status=:status,
observacoes=:obs
WHERE codservico=:id";

$stmt = $conexao->prepare($sql);

$stmt->execute([
  ':id' => $_POST['id'],
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