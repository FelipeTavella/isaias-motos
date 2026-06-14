<?php

require_once("../../Conexao/conexao.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];
    $estoque = $_POST['estoque'];
    $localizacao = $_POST['localizacao'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO pecas
    (
        nome,
        marca,
        preco_medio,
        categoria,
        estoque,
        localizacao,
        descricao
    )
    VALUES
    (
        :nome,
        :marca,
        :preco,
        :categoria,
        :estoque,
        :localizacao,
        :descricao
    )";

    $stmt = $conexao->prepare($sql);

    $stmt->bindParam(':nome',$nome);
    $stmt->bindParam(':marca',$marca);
    $stmt->bindParam(':preco',$preco);
    $stmt->bindParam(':categoria',$categoria);
    $stmt->bindParam(':estoque',$estoque);
    $stmt->bindParam(':localizacao',$localizacao);
    $stmt->bindParam(':descricao',$descricao);

    $stmt->execute();

    header("Location: ../pecas.php");
    exit;
}

?>