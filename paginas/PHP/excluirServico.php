<?php
require_once("../../Conexao/conexao.php");

$id = $_GET['id'];

$sql = "DELETE FROM servicos WHERE codservico = :id";
$stmt = $conexao->prepare($sql);
$stmt->execute([':id' => $id]);

header("Location: ../servicos.php");
?>