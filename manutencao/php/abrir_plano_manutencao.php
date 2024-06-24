<?php

require ('valida_sessao.php');
include('sidebar.php');
include("../../conexao.php");

   

    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];

 //SQL DEFAULT TRAS OS EQUIPAMENTO
    $sql_code = "SELECT * FROM equipamentos"; 
    $sql_code = $conn->prepare($sql_code);
    $sql_code->execute();
    $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 

    //SQL DEFAULT TRAS OS COLABORADORES
    $sql_code_user = "SELECT * FROM usuarios WHERE nivel = '3'"; 
    $sql_code_user = $conn->prepare($sql_code_user);
    $sql_code_user->execute();
    $results_user = $sql_code_user->fetchAll(PDO::FETCH_ASSOC); 



   
    

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //TRATAR DADOS VAZIOS
    if(!empty($dados)){
        

        if (!empty($dados['nome_equip']) && !empty($dados['ocorrencia']) && !empty($dados['data_exec'])  && !empty($dados['periodicidade'])){

        

            if($agora_liquido > date('Y/m/d H:i:s', strtotime($dados["data_exec"]))){
                ?> <script> alert("Data programada NÃO pode ser retroativa!");</script><?php 
            }else{
               
                //INSERT NO BANCO

                $sql_form = "INSERT INTO planos_manutencao (id_equipamento, data_inicio_exec, periodicidade, servicos, id_colab_execucao, id_criador, data_criacao) VALUES (:id_equipamento, :data_inicio_exec, :periodicidade, :servicos, :id_colab_execucao, :id_criador, :data_criacao)"; 
                $sql_form = $conn->prepare($sql_form);

                $sql_form->bindParam(':id_equipamento', $dados['nome_equip']); 
                $sql_form->bindParam(':data_inicio_exec', $dados['data_exec']);
                $sql_form->bindParam(':periodicidade', $dados['periodicidade']);
                $sql_form->bindParam(':servicos', $dados['ocorrencia']);
                $sql_form->bindParam(':id_colab_execucao', $dados['nome_colab']);
                $sql_form->bindParam(':id_criador', $_SESSION['id_session']);
                $sql_form->bindParam(':data_criacao', $agora_liquido);                               
                    
                
                $sql_form->execute();
                ?><script> alert("Plano de manutenção criado com sucesso!"); redirecionarAbrirOS();</script>
                <?php 
        
            }

        

        
        
       
        
    }else{
        ?> <script> alert("Todo o preenchimento da criação do plano são obrigatórios!"); </script> <?php
    }
}

       
   
       

    


?>
<head>
 <link rel="stylesheet" href="../css/style_abrir_plano_manutencao.css">
 <script defer src="../js/script_abriros.js"></script>
</head>
<main id="main" class="main" >
    
    <div class="central">
        <h2>Criação Plano Manutenção</h2>
        <form enctype="multipart/form-data" method="POST" id="abriros_form" action="">
           
                  <fieldset>
                    <legend>Equipamento</legend>

                    

                    <select  id=""  name="nome_equip" required>
                        <option value=""></option>
                    <?php foreach ($results as $row) { 
                        echo '<option value="' . $row["id"] . '">' . $row["nome_equip"] . '</option>';
                     } ?>
            
                    </select>
                  </fieldset>

                  <fieldset >
                    <legend>Data de Início de Execução</legend>
                    <input type="datetime-local" name="data_exec" class="data_prog">

                  </fieldset>

                 

                  <fieldset>    
                    <legend>Colaborador para execução</legend>

                        <select  id=""  name="nome_colab" required>
                            <option value=""></option>
                        <?php foreach ($results_user as $row) { 
                            echo '<option value="' . $row["ID"] . '">' . $row["nome"] . '</option>';
                        } ?>
                        </select>

                  </fieldset>
                 
                  <fieldset>
                    <legend>Periodicidade</legend>

                    

                    <select  id=""  name="periodicidade" required>
                        <option value=""></option>
                        <option value="15">A cada 15 dias</option>
                        <option value="30">A cada 30 dias</option>
                        <option value="90">A cada 90 dias</option>
                        <option value="180">A cada 6 meses</option>
                    
            
                    </select>
                  </fieldset>

                  
           

                  <fieldset >
                    <legend>Serviços a serem executados</legend>
                    <textarea id=""  name="ocorrencia" rows="4" cols="40" required></textarea>

                  </fieldset>
                 

                  <input type="button" id="abrir" value="Criar" onclick="confirmaCria()">



          
                    
                    
                    
        </form>
    </div>
</main>