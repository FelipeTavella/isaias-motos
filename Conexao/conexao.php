<?php

    $bd = "isaias_motos";
    $usuario = "root";
    $senha = "";
    $host = "localhost";


    try {
        $conexao = new PDO("mysql:host=$host;dbname=$bd", $usuario, $senha);
    } catch(PDOException $e) {
        echo "Conexão falhou: " . $e->getMessage();
        exit;
    }


?> 

<!-- <?php

//criando uma classe 

class Conexao{
    //atributos a serem utilizados pela classe
  private $usuario;
  private $senhabd;
  private $host;
  private $bd;
  private $conexao;
  
  //estabelecendo um método construtor da classe
    //função criada para determinar valores quando a classe for executada, esses dados sempre estao preparados porem so iram ser utilizados quando for chamado
  function __construct(){
     $this->usuario="root";
     $this->senhabd="";
     $this->host="localhost";
     $this->bd="isaias_motos";
}
 
  public function Conectar(){
try {
  $this->conexao = new PDO("mysql:host=$this->host;dbname=$this->bd",$this->usuario,$this->senhabd,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $this->conexao;
 } catch (Exception $erro) {
   echo $erro->getMessage();
    }
  }
}
?> -->
