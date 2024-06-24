<?php

require ('valida_sessao.php');
include('sidebar.php');
include("../../conexao.php");


    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];

    if(isset($_GET)){
        $id = $_GET['id'];
    
        $sql_code = "SELECT 
        os_corretiva.id,
        equipamentos.nome_equip,
        os_corretiva.data_abertura,
        os_corretiva.data_fechamento,
        os_corretiva.ocorrencia,
        os_corretiva.comentario_fechamenrto,
        os_corretiva.data_programada,
        usuarios_abertura.nome AS nome_abertura,
        usuarios_fechamento.nome AS nome_fechamento,
        os_corretiva.path
        
           FROM os_corretiva
           
           JOIN equipamentos ON os_corretiva.id_equipamento = equipamentos.id
           LEFT JOIN usuarios AS usuarios_abertura ON os_corretiva.id_user_abertura = usuarios_abertura.id
           LEFT JOIN usuarios AS usuarios_fechamento ON os_corretiva.id_user_fechamento = usuarios_fechamento.id
               
           WHERE os_corretiva.id = $id;"; 
   
    
       $sql_code = $conn->prepare($sql_code);
       $sql_code->execute();
       $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 
   
        
    }

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if($dados != NULL){
        if(!empty($dados['data_exec'])){

            if($agora_liquido > date('Y/m/d H:i:s', strtotime($dados["data_exec"]))){
                ?> <script> alert("Data programada NÃO pode ser retroativa!");</script><?php 
            }else{
                echo"--------------------foi";
            }
            
        }else{
            ?> <script> alert("Nova Data Programada é obrigatória!"); </script> <?php
   
        }
    }
    


?>
<head>
 <link rel="stylesheet" href="../css/style_alterar_plano_manutencao.css">
 <script defer src="../js/script_abriros.js"></script>
</head>
<main id="main" class="main" >
    
    <div class="central">
        <h2>Alterar Data da Programação</h2>
        <form enctype="multipart/form-data" method="POST" id="abriros_form" action="">
           <?php foreach($results as $row){ ?>

                    <div class="dados_existentes">
                    
                        <legend>Número do Planejamento</legend>

                        <h3><?php echo $row['id']; ?></h3>
                        <br>

                        
                    
                    
                        <legend>Equipamento</legend>

                        <h3><?php echo $row['nome_equip']; ?></h3>
                        <br>

                        
                    
                    
                        <legend>Data Programada Atual</legend>

                        <h3><?php echo date('d-m-Y H:i', strtotime($row["data_programada"])) ?></h3>
                        <br>

                        
                    

                    
                        <legend>Ocorrencia</legend>
                        <p><?php echo $row['ocorrencia']; ?></p>
                        <br>

                    </div>

                  


                  <fieldset>
                    <legend id="nova_prog">Nova Data Programada</legend>
                    <input type="datetime-local" name="data_exec" class="data_prog">

                  </fieldset>
                 

                  
           

                 
                  
                <?php } ?>
                  <input type="button" id="abrir" value="Alterar Data" onclick="confirmaAlteraOS()">



          
                    
                    
                    
        </form>
    </div>
</main>