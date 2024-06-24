<?php

require ('valida_sessao.php');
include('sidebar.php');
include("../../conexao.php");


    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];

 

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    



       
                
    

    if (!empty($dados)) {
        if (!empty($dados['nome_equip'])) {
    
            // Consulta para verificar se o equipamento já está cadastrado
            $sql_check = "SELECT nome_equip FROM equipamentos WHERE nome_equip = :nome_equip";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bindParam(':nome_equip', $dados['nome_equip']);
            $stmt_check->execute();
            $results = $stmt_check->fetchAll(PDO::FETCH_ASSOC);
    
            if (!empty($results)) {
                
                echo "<script> alert('Equipamento NÃO CADASTRADO! Nome já existente.'); </script>";
            } else {
             
                $sql_form = "INSERT INTO equipamentos (nome_equip, id_user_criacao, data_criacao) VALUES (:nome_equip, :id_user_criacao, :data_criacao)";
                $stmt_form = $conn->prepare($sql_form);
                $stmt_form->bindParam(':nome_equip', $dados['nome_equip']);
                $stmt_form->bindParam(':id_user_criacao', $_SESSION['id_session']);
                $stmt_form->bindParam(':data_criacao', $agora_liquido);
    
                $stmt_form->execute();
                echo "<script> alert('Equipamento cadastrado com sucesso!'); redirecionarEquip(); </script>";
            }
        }
    }
    



    


       
   
       

    


?>
<head>
 <link rel="stylesheet" href="../css/style_cadastra_equip.css">
 <script defer src="../js/script_cadastra_equip.js"></script>
</head>
<main id="main" class="main" >
    
    <div class="central">
        <h2>Cadastro de Equipamentos</h2>
        <form enctype="multipart/form-data" method="POST" id="abriros_form" action="">
           
                  <fieldset>
                    <legend>Equipamento</legend>

                    <input type="text" name="nome_equip"  required>

                    
                  </fieldset>

                  
           


                  <input type="button" id="abrir" value="Cadastrar" onclick="confirmaOS()">



          
                    
                    
                    
        </form>
    </div>
</main>