<?php
require_once("../../Conexao/conexao.php");

$codordem = $_POST['codordem'];
$status = $_POST['status'];
$data = $_POST['data_fechamento'];

$sql = "
UPDATE ordens_servico 
SET status = ?, data_fechamento = ?
WHERE codordem = ?
";

$stmt = $conexao->prepare($sql);
$stmt->execute([$status, $data, $codordem]);

header("Location: ../ordens.php");

?>