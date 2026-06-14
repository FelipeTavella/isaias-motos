<?php

require_once("../../Conexao/conexao.php");

$id = $_GET['id'];

$stmt = $conexao->prepare("DELETE FROM motos WHERE codmoto = ?");
$stmt->execute([$id]);

header("Location: ../motos.php");
exit;

?>