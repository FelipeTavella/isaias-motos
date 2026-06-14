<?php

require_once("../../Conexao/conexao.php");

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $sql = "DELETE FROM pecas
            WHERE codpeca = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindParam(':id',$id);

    $stmt->execute();
}

header("Location: ../pecas.php");
exit;
?>