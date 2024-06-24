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
    



    if(!empty($dados)){
        if (!empty($dados['nome_equip']) && !empty($dados['ocorrencia']) && !empty($dados['nome_colab']) && !empty($dados['data_prev'])){

            $arquivo = $_FILES['arquivo'];

         //SQL PARA TRATAR ARQUIVOS
            if(!empty($arquivo['name'])){

                

                
               
                if($arquivo['error'])
                    die('Falha ao enviar arquivo!');

                $pasta = "arquivos/";
                $nomeDoArquivo = $arquivo['name'];
                $novoNomeDoArquivo = uniqid();
                $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));
                $path = $pasta . $novoNomeDoArquivo . "." . $extensao;
                $deu_certo = move_uploaded_file($arquivo["tmp_name"], $path);
               


                $sql_form = "INSERT INTO os_corretiva (data_abertura, id_equipamento, id_user_abertura, id_colab_execucao, ocorrencia, data_prev_fecha, nome_arquivo, path) VALUES (:data_abertura, :id_equipamento, :id_user_abertura, :id_colab_execucao, :ocorrencia, :data_prev_fecha, :nome_arquivo, :path)"; 
                $sql_form = $conn->prepare($sql_form);
                $sql_form->bindParam(':data_abertura', $agora_liquido);
                $sql_form->bindParam(':id_equipamento', $dados['nome_equip']);
                $sql_form->bindParam(':id_user_abertura', $_SESSION['id_session']);
                $sql_form->bindParam(':id_colab_execucao', $dados['nome_colab']);
                $sql_form->bindParam(':ocorrencia', $dados['ocorrencia']);
                $sql_form->bindParam(':data_prev_fecha', $dados['data_prev']);
                $sql_form->bindParam(':nome_arquivo', $novoNomeDoArquivo);
                $sql_form->bindParam(':path', $path);

                $sql_form->execute();
                ?> <script> alert("Ordem de serviço aberta!"); redirecionarAbrirOS();</script><?php 
                

            }elseif(empty($arquivo['name'])){ //CASO NAO TENHA ARQUIVO ANEXADO

                


                $sql_form = "INSERT INTO os_corretiva (data_abertura, id_equipamento, id_user_abertura, id_colab_execucao, ocorrencia, data_prev_fecha) VALUES (:data_abertura, :id_equipamento, :id_user_abertura, :id_colab_execucao, :ocorrencia, :data_prev_fecha)"; 
                $sql_form = $conn->prepare($sql_form);
                $sql_form->bindParam(':data_abertura', $agora_liquido);
                $sql_form->bindParam(':id_equipamento', $dados['nome_equip']);
                
                $sql_form->bindParam(':id_user_abertura', $_SESSION['id_session']);
                $sql_form->bindParam(':id_colab_execucao', $dados['nome_colab']);
                $sql_form->bindParam(':ocorrencia', $dados['ocorrencia']);
                $sql_form->bindParam(':data_prev_fecha', $dados['data_prev']);
                $sql_form->execute();
                ?><script> alert("Ordem de serviço aberta!"); redirecionarAbrirOS();</script>
                <?php 
        
            }

        
        
       
        
    }else{
        ?> <script> alert("Equipamento, Ocorrencia, Colaborador de Execução e Data Prevista de fechamento são obrigatórios!"); </script> <?php
    }
}
       
   
       

    


?>
<head>
 <link rel="stylesheet" href="../css/style_abriros.css">
 <script defer src="../js/script_abriros.js"></script>
</head>
<main id="main" class="main" >
    
    <div class="central">
        <h2>Abertura de Ordem de Serviço Corretiva</h2>
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

                  <fieldset>    
                    <legend>Colaborador para execução</legend>

                        <select  id=""  name="nome_colab" required>
                            <option value=""></option>
                        <?php foreach ($results_user as $row) { 
                            echo '<option value="' . $row["ID"] . '">' . $row["nome"] . '</option>';
                        } ?>
                        </select>

                  </fieldset>

                  <fieldset >
                    <legend>Data Prevista para fechamento</legend>
                    <input type="datetime-local" name="data_prev" class="data_prog">

                  </fieldset>

                  
           

                  <fieldset >
                    <legend>Ocorrencia</legend>
                    <textarea id=""  name="ocorrencia" rows="4" cols="40" required></textarea>

                  </fieldset>
                  <fieldset>
                    <legend>Imagem/Vídeo <span class="mark">(Opcional)</span></legend>
                    <input type="file" name="arquivo" id="input">

                  </fieldset>

                  <input type="button" id="abrir" value="Abrir" onclick="confirmaOS()">



          
                    
                    
                    
        </form>
    </div>
</main>