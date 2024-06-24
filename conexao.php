<?php 

$hostname = "localhost";
$bancodedados = "";
$usuario = "root";
$senha = "";

try{
    $conn = new PDO("mysql:host=$hostname;dbname=" . $bancodedados, $usuario, $senha);
    //echo "Conectado";

}catch(PDOException $err){
    echo "Erro" . $err->getMessage();
}

// <?php 

// $hostname = "mysql.ocaixeiroprint.com.br";
// $bancodedados = "ocaixeiroprint01";
// $usuario = "ocaixeiroprint01";
// $senha = "Matheus1234";

// try{
//     $conn = new PDO("mysql:host=$hostname;dbname=" . $bancodedados, $usuario, $senha);
//     //echo "Conectado";

// }catch(PDOException $err){
//     echo "Erro" . $err->getMessage();
// }