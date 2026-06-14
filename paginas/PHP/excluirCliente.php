<?php

require_once("../../Conexao/conexao.php");

$id = $_GET['id'];

$sql = "DELETE FROM clientes WHERE coduser = :id";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

header("Location: ../clientes.php");
exit;

?>