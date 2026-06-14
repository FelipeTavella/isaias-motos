<?php

require_once("../../Conexao/conexao.php");

$sql = "INSERT INTO motos (placa, modelo, marca, ano, coduser)
VALUES 
(:placa, :modelo, :marca, :ano, :coduser)";

$stmt = $conexao->prepare($sql);
$stmt->execute([
  ':placa' => $_POST['placa'],
  ':modelo' => $_POST['modelo'],
  ':marca' => $_POST['marca'],
  ':ano' => $_POST['ano'],
  ':coduser' => $_POST['coduser']
]);

header("Location: ../motos.php");
exit;

?>