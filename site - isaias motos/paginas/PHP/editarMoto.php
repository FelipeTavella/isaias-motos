<?php

require_once("../../Conexao/conexao.php");

$sql = "UPDATE motos
SET placa=:placa, modelo=:modelo, marca=:marca, ano=:ano
WHERE codmoto=:id";

$stmt = $conexao->prepare($sql);
$stmt->execute([
  ':id' => $_POST['id'],
  ':placa' => $_POST['placa'],
  ':modelo' => $_POST['modelo'],
  ':marca' => $_POST['marca'],
  ':ano' => $_POST['ano']
]);

header("Location: ../motos.php");
exit;

?>