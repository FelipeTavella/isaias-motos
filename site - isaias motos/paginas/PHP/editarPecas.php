<?php

require_once("../../Conexao/conexao.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $id = $_POST['id'];

    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];
    $estoque = $_POST['estoque'];
    $localizacao = $_POST['localizacao'];
    $descricao = $_POST['descricao'];

    $sql = "UPDATE pecas SET

        nome = :nome,
        marca = :marca,
        preco_medio = :preco,
        categoria = :categoria,
        estoque = :estoque,
        localizacao = :localizacao,
        descricao = :descricao

    WHERE codpeca = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindParam(':nome',$nome);
    $stmt->bindParam(':marca',$marca);
    $stmt->bindParam(':preco',$preco);
    $stmt->bindParam(':categoria',$categoria);
    $stmt->bindParam(':estoque',$estoque);
    $stmt->bindParam(':localizacao',$localizacao);
    $stmt->bindParam(':descricao',$descricao);
    $stmt->bindParam(':id',$id);

    $stmt->execute();

    header("Location: ../pecas.php");
    exit;
}

?>