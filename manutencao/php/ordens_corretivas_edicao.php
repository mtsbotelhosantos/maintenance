<?php

require ('valida_sessao.php');
//include('sidebar.php');
include("../../conexao.php");


   

    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];
    

 





     // SQL PARA TRAZER AS OS

    
     if(!empty($_GET['id'])){

        

    $id = $_GET['id'];
    
     $sql_code = "SELECT 
     os_corretiva.id,
     equipamentos.nome_equip,
     os_corretiva.data_abertura,
     os_corretiva.data_fechamento,
     os_corretiva.ocorrencia,
     os_corretiva.comentario_fechamenrto,
     os_corretiva.data_prev_fecha,
     os_corretiva.id_plano_preventiva,
     usuarios_responsavel.nome AS responsavel,
     usuarios_abertura.nome AS nome_abertura,
     usuarios_fechamento.nome AS nome_fechamento,
     os_corretiva.path
     
        FROM os_corretiva
        
        JOIN equipamentos ON os_corretiva.id_equipamento = equipamentos.id
        LEFT JOIN usuarios AS usuarios_abertura ON os_corretiva.id_user_abertura = usuarios_abertura.id
        LEFT JOIN usuarios AS usuarios_fechamento ON os_corretiva.id_user_fechamento = usuarios_fechamento.id
        LEFT JOIN usuarios AS usuarios_responsavel ON os_corretiva.id_colab_execucao = usuarios_responsavel.id
            
        WHERE os_corretiva.id = $id;"; 

 
    $sql_code = $conn->prepare($sql_code);
    $sql_code->execute();
    $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 



       //SQL TRAZER APONTAMENTO
       $sql_apto = "SELECT 
       apontamentos.id,
       apontamentos.data_inicio,
       apontamentos.data_fim,
       apontamentos.obs_prestador,
       usuarios.nome AS nome_prestador,
       usuarios.id
        FROM 
            apontamentos
        JOIN 
            usuarios ON apontamentos.id_prestador = usuarios.id
        WHERE 
            apontamentos.id_os = '$id';"; 

 
       $sql_apto = $conn->prepare($sql_apto);
       $sql_apto->execute();
       $results_apto = $sql_apto->fetchAll(PDO::FETCH_ASSOC); 

       

       

      
    

        //SQL TRAZER PARADAS

        $sql_parada = "SELECT 
        paradas.id,
        paradas.data_inicio_parada,
        paradas.data_fim_parada,
        paradas.motivo_parada,
        usuarios.nome AS nome_prestador
         FROM 
            paradas
         JOIN 
             usuarios ON paradas.id_prestador = usuarios.id
         WHERE 
             paradas.id_os = $id AND paradas.id_prestador = '$usuario';"; 

 
       $sql_parada = $conn->prepare($sql_parada);
       $sql_parada->execute();
       $results_parada = $sql_parada->fetchAll(PDO::FETCH_ASSOC); 

       //ATIVAR OU DESATIVAR BOTOES DE ACORDO COM APONTAMENTO
       $parada_ini = false;
       $apto_ini = false;
       $apto_fim = false;
       $os_aberta = false;

       if(!empty($results)){
        foreach($results as $row){
            if(empty($row['data_fechamento'])){
                $os_aberta = true;
            }else{
                $os_aberta = false;
            }
        }
       }
        if(!empty($results_apto)){
            
            foreach($results_apto as $row_apontamento){
                if($row_apontamento['data_inicio'] == NULL){
                    $apto_ini = true;
                }elseif($row_apontamento['data_fim'] != NULL){
                    
                    $apto_fim = true;
                }else{
                    $parada_ini = true;
                }
                
             
             }
        }else{
            $apto_ini = true;
        }

        if(!empty($results_parada)){

           foreach ($results_parada as $row_parada){
            $fim_parada = $row_parada['data_fim_parada'];

            if($fim_parada == NULL || empty($fim_parada)){
                $parada_ini = false;

            }else{
                $parada_ini = true;
            }
        }}
       

    

  
    
        
    
    
        
    
}     
           
       

       



    if(!empty($_POST)){
        //  echo("-----------------------");
        //  var_dump($_POST);
         if($_POST['nome'] == "Finalizar Ordem de Serviço"){

           //SQL TRAZER APONTAMENTOS GERAL
           $sql_apto = "SELECT 
           apontamentos.id,
           apontamentos.data_inicio,
           apontamentos.data_fim,
           apontamentos.obs_prestador,
           usuarios.nome AS nome_prestador,
           usuarios.id
               FROM 
                   apontamentos
               JOIN 
                   usuarios ON apontamentos.id_prestador = usuarios.id
               WHERE 
                   apontamentos.id_os = '$id';";

       
               $sql_apto = $conn->prepare($sql_apto);
               $sql_apto->execute();
               $results_apto_geral = $sql_apto->fetchAll(PDO::FETCH_ASSOC); 

            
            foreach($results_apto_geral as $row_apontamento){
                if(empty($row_apontamento['data_fim'])){
                    
                    ?><script>alert("Ordem de Serviço possui apontamento em aberto. Não pode ser finalizada!");</script><?php
                   

                    

                }else{
                    foreach($results as $row){

                    
                        if(empty($row['data_fechamento'])){
                            $id_os = $id;
                            $id_fechamento = $usuario;
                            $comentario_fechamento = $_POST['comentario'];
                            $data_fechamento = $agora_liquido;

                            

                            $sql_form = "UPDATE os_corretiva SET data_fechamento='$data_fechamento', id_user_fechamento='$id_fechamento', comentario_fechamenrto='$comentario_fechamento' WHERE id='$id_os'";

                            $sql_form = $conn->prepare($sql_form);
                                
                            $sql_form->execute();
                            
                            ?><script>alert("OS Fechada com sucesso!");</script><?php

                            header('Refresh:0');

                        }else{
                            ?><script>alert("Ordem de Serviço já foi finalizada na data: <?php echo date('d-m-Y H:i', strtotime($row["data_fechamento"])); ?>");</script><?php
                    
                        }
                    }
                }
            }

            

         }elseif($_POST['nome'] == "Iniciar Apontamento"){


             //SQL TRAZER APONTAMENTOS SEM PARADAS EM ABERTAS
             $sql_apto = "SELECT 
             a.Id AS apontamento_id,
             a.id_prestador AS apontamento_id_prestador,
             a.data_inicio AS apontamento_data_inicio,
             a.data_fim AS apontamento_data_fim,
             a.tempo_apontado AS apontamento_tempo_apontado,
             a.id_os AS apontamento_id_os
                FROM 
                    APONTAMENTOS a
                JOIN 
                    PARADAS p ON a.id_os = p.id_os
                WHERE 
                    a.data_fim IS NULL
                    AND a.id_prestador = $usuario
                    AND NOT EXISTS (
                        SELECT 1
                        FROM PARADAS p2
                        WHERE p.id_os = p2.id_os
                        AND p2.data_fim_parada IS NULL
                    );";

         
                 $sql_apto = $conn->prepare($sql_apto);
                 $sql_apto->execute();
                 $results_apto_geral = $sql_apto->fetchAll(PDO::FETCH_ASSOC); 

                  //SQL TRAZER APONTAMENTOS EM ABERTOS
                 $sql_apto2 = "SELECT 
                 apontamentos.id,
                 apontamentos.data_inicio,
                 apontamentos.data_fim,
                 apontamentos.obs_prestador
             
                     FROM 
                         apontamentos
                         
                
                 
                     WHERE 
                         apontamentos.id_prestador = $usuario
                         AND apontamentos.data_fim IS NULL;";

             
                     $sql_apto2 = $conn->prepare($sql_apto2);
                     $sql_apto2->execute();
                     $results_apto_em_aberto = $sql_apto2->fetchAll(PDO::FETCH_ASSOC);

                     //SQL TRAZER APONTAMENTOS ABERTOS SEM PARADAS INICIADAS
                     $sql_apto3 = "SELECT 
                     apontamentos.id,
                     apontamentos.data_inicio,
                     apontamentos.data_fim,
                     apontamentos.obs_prestador,
                     apontamentos.id_os
                 FROM 
                     apontamentos
                 WHERE 
                     apontamentos.id_prestador = $usuario
                     AND apontamentos.data_fim IS NULL
                     AND NOT EXISTS (
                         SELECT 1
                         FROM paradas
                         WHERE apontamentos.id_os = paradas.id_os
                     );";

                    $sql_apto3 = $conn->prepare($sql_apto3);
                    $sql_apto3->execute();
                    $results_apto_em_aberto_sem_parada = $sql_apto3->fetchAll(PDO::FETCH_ASSOC);

            

                //################   TRATAR ESSA PARTE DE BLOQUEAR APONTAMENTOS SE TIVER AP ABERTO
                //trocar o IF     if(!empty($results_apto_aberto)){
                   
                if(!empty($results_apto_em_aberto)){

                   
                   if(!empty($results_apto_geral)){
                      
                    foreach ($results_apto_geral as $parada_fechada){
                        

                    echo"<script>alert('Você possui apontamento aberto com parada fechada na OS-". $parada_fechada['apontamento_id_os']." Inicie a parada novamente ou finalize o apontamento para conseguir apontar!');</script>";
                        // header('Refresh:0');
                    
                    }
                   }else if(!empty($results_apto_em_aberto_sem_parada)){

                    foreach ($results_apto_em_aberto_sem_parada as $parada_apt_ab){
                  
                        echo"<script>alert('É necessário iniciar uma parada ou finalizar o apontamento da OS-". $parada_apt_ab['id_os']." para conseguir apontar!');</script>";
                    }
                    }else{
                    $sql_form = "INSERT INTO apontamentos (id_prestador, data_inicio, id_os)
                    VALUES (:usuario, :agora_liquido, :id)";
    
                        $sql_form = $conn->prepare($sql_form);
                        $sql_form->bindParam(':usuario', $usuario);
                        $sql_form->bindParam(':agora_liquido', $agora_liquido);
                        $sql_form->bindParam(':id', $id);
    
                        $sql_form->execute();
    
                        header('Refresh:0');

                   }
                
                  
                }else{
                    
                        $sql_form = "INSERT INTO apontamentos (id_prestador, data_inicio, id_os)
                VALUES (:usuario, :agora_liquido, :id)";

                    $sql_form = $conn->prepare($sql_form);
                    $sql_form->bindParam(':usuario', $usuario);
                    $sql_form->bindParam(':agora_liquido', $agora_liquido);
                    $sql_form->bindParam(':id', $id);

                    $sql_form->execute();

                    header('Refresh:0');

                }

            

            
          



         }elseif($_POST['nome'] == "Iniciar Parada Apontamento"){

            $ocorrencia = $_POST['ocorrencia'];
            
            $sql_form = "INSERT INTO paradas (id_prestador, data_inicio_parada, id_os, motivo_parada)
                VALUES (:usuario, :agora_liquido, :id, :motivo)";

            $sql_form = $conn->prepare($sql_form);
            $sql_form->bindParam(':usuario', $usuario);
            $sql_form->bindParam(':agora_liquido', $agora_liquido);
            $sql_form->bindParam(':id', $id);
            $sql_form->bindParam(':motivo', $ocorrencia);

            $sql_form->execute();

            header('Refresh:0');


         }elseif($_POST['nome'] == "Retornar Apto"){

            //SQL TRAZER APONTAMENTOS SEM PARADAS EM ABERTAS
            $sql_apto = "SELECT 
            a.Id AS apontamento_id,
            a.id_prestador AS apontamento_id_prestador,
            a.data_inicio AS apontamento_data_inicio,
            a.data_fim AS apontamento_data_fim,
            a.tempo_apontado AS apontamento_tempo_apontado,
            a.id_os AS apontamento_id_os
               FROM 
                   APONTAMENTOS a
               JOIN 
                   PARADAS p ON a.id_os = p.id_os
               WHERE 
                   a.data_fim IS NULL
                   AND a.id_prestador = $usuario
                   AND NOT EXISTS (
                       SELECT 1
                       FROM PARADAS p2
                       WHERE p.id_os = p2.id_os
                       AND p2.data_fim_parada IS NULL
                   );";

        
                $sql_apto = $conn->prepare($sql_apto);
                $sql_apto->execute();
                $results_apto_geral = $sql_apto->fetchAll(PDO::FETCH_ASSOC); 

               

                    //SQL TRAZER APONTAMENTOS ABERTOS SEM PARADAS INICIADAS
                    $sql_apto3 = "SELECT 
                    apontamentos.id,
                    apontamentos.data_inicio,
                    apontamentos.data_fim,
                    apontamentos.obs_prestador,
                    apontamentos.id_os
                FROM 
                    apontamentos
                WHERE 
                    apontamentos.id_prestador = $usuario
                    AND apontamentos.data_fim IS NULL
                    AND NOT EXISTS (
                        SELECT 1
                        FROM paradas
                        WHERE apontamentos.id_os = paradas.id_os
                    );";

                   $sql_apto3 = $conn->prepare($sql_apto3);
                   $sql_apto3->execute();
                   $results_apto_em_aberto_sem_parada = $sql_apto3->fetchAll(PDO::FETCH_ASSOC);
                   
                  

                   
                    if(!empty($results_apto_geral)){
                       
                     foreach ($results_apto_geral as $parada_fechada){
                         
 
                     echo"<script>alert('Você possui apontamento aberto com parada fechada na OS-". $parada_fechada['apontamento_id_os']." Inicie a parada novamente ou finalize o apontamento para conseguir apontar!');</script>";
                         // header('Refresh:0');
                     
                     }
                    }else if(!empty($results_apto_em_aberto_sem_parada)){
 
                     foreach ($results_apto_em_aberto_sem_parada as $parada_apt_ab){
                   
                         echo"<script>alert('É necessário iniciar uma parada ou finalizar o apontamento da OS-". $parada_apt_ab['id_os']." para conseguir apontar!');</script>";
                     }
                     }else{
                        $id_apt_parada = $_POST['id_apt_parada'];
           
                        $sql_form = "UPDATE paradas SET data_fim_parada = :data_fim_parada
                            WHERE id_os = :id AND id_prestador = :id_prestador AND data_inicio_parada IS NOT NULL AND id = :id_apt_parada;";
            
                        $sql_form = $conn->prepare($sql_form);
                        $sql_form->bindParam(':data_fim_parada', $agora_liquido);
                        $sql_form->bindParam(':id', $id);
                        $sql_form->bindParam(':id_prestador', $usuario);
                        $sql_form->bindParam(':id_apt_parada', $id_apt_parada);
            
                        $sql_form->execute();
            
                        header('Refresh:0');
 
                    }
                 
                   
                
            //PARADA CORRETA
            


         }elseif($_POST['nome'] == "Finalizar Apontamento"){

            $obs_prestador = $_POST['obs_fecha_app'];
            
            $sql_form = "UPDATE apontamentos SET data_fim = :data_fim, obs_prestador = :obs_prestador
                WHERE id_os = :id AND id_prestador = :id_prestador AND data_fim IS NULL;";

            $sql_form = $conn->prepare($sql_form);
            $sql_form->bindParam(':data_fim', $agora_liquido);
            $sql_form->bindParam(':obs_prestador', $obs_prestador);
            $sql_form->bindParam(':id', $id);
            $sql_form->bindParam(':id_prestador', $usuario);

            $sql_form->execute();

            header('Refresh:0');
         }elseif($_POST['nome'] == "Alterar Data"){

            if(!empty($_POST['data_prev'])){
                
                $sql_form = "UPDATE os_corretiva SET data_prev_fecha = :data_prev_fecha
                WHERE id = :id;";

                $sql_form = $conn->prepare($sql_form);
                $sql_form->bindParam(':data_prev_fecha', $_POST['data_prev']);
                $sql_form->bindParam(':id', $id);
                $sql_form->execute();

                header('Refresh:0');

            }else{
                echo"<script>alert('Campo não pode ser vazio!');</script>";
                     

            }

           
           
         }
    }



    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="../css/style_sidebar.css"> -->
    <link rel="stylesheet" href="../css/style_ordens_corretivas_edicao.css">
    <script defer src="../js/script_corretiva_edicao.js"></script>
    <style>
        .tipo_os_corretiva{
            background-color: rgb(12, 206, 141);
            padding: 2px;
            margin: 2px;
            font-weight: bold;
            font-size: smaller;
            color: rgb(72, 72, 72);
            border-radius: 4px;

        }
        .tipo_os_preventiva{
            background-color: rgb(245, 214, 15);
            padding: 2px;
            margin: 2px;
            font-weight: bold;
            font-size: smaller;
            color: rgb(72, 72, 72);
            border-radius: 4px;

        }
    </style>
    <title>Ordens Corretivas</title>
   </head>
        <body>
            
        
            <main>
            
                <a href="inicio.php" class="close-window">
                                <span class="material-symbols-outlined">close</span>
                                </a>
                

            <?php foreach ($results as $row) {
                
                if($row['id_plano_preventiva'] != null){
                    $tipo_os = 1; //preventiva
                }else{
                    $tipo_os = 2; //corretiva
                }  ?>
               
            <div class="principal">

                <div class="<?php if(!empty($row["data_fechamento"])) {echo "numero_os_close"; } else {echo"numero_os";}?>">Fechada</div>
                <div class="header">
                        

                                <div class="info-os">
                                    <span> <label class="titulos_os">OS:</label> <?php echo $row['id'] ?> <p class="<?php if($tipo_os == 1){echo"tipo_os_preventiva";}else{{echo"tipo_os_corretiva";} } ?>"><?php if($tipo_os == 1){echo"Preventiva";}else{{echo"Corretiva";} } ?></p></span>
                                </div>
                                <div class="info-os">
                                    <span><label class="titulos_os">Equipamento:</label><?php echo $row['nome_equip'] ?></span> 
                                    <br>
                                    <span><label class="titulos_os">Colaborador:</label><?php echo $row['responsavel'] ?></span>
                                </div>
                            
                            
                                <div class="info-os">
                                    <span><label class="titulos_os">Data Abertura:</label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?></span>
                                    <span><label class="titulos_os">Data Final Prevista:</label> <?php echo date('d-m-Y H:i', strtotime($row["data_prev_fecha"])) ?> <button id="edit_data" class="material-symbols-outlined" onclick="NewDatePrev()">edit</button> </span>
                                    
                                        <form id="altera_data_prev" action="" method="POST" name="altera_data_prev_fecha" style="display: none;">
                                            <input type="datetime-local" name="data_prev" class="">
                                            <input class="" type="submit" value="Alterar Data" name="nome">
                                            
                                        </form>
                                        <br>

                                    <span><label class="titulos_os">Usuário Abertura:</label><?php echo $row['nome_abertura'] ?></span>
                                   
                                    <span><label class="titulos_os">Ocorrencia:</label><?php echo $row['ocorrencia'] ?></span>
                                </div>
                                
                                <div class="<?php if(!empty($row["data_fechamento"])) {echo "info-os"; } else {echo"numero_os"; }?>">
                                    <span><label class="titulos_os">Data Fechamento:</label> <?php if(!empty($row["data_fechamento"]))
                                                                            {echo date('d-m-Y H:i', strtotime($row["data_fechamento"])); } else {echo""; }?>
                                                                            </span>
                                  
                                
                                    <span><label class="titulos_os">Usuário Fechamento:</label><?php if(!empty($row["nome_fechamento"]))
                                                                            {echo $row["nome_fechamento"]; } else {echo""; }?>
                                                                            </span>
                                  
                                    <span><label class="titulos_os">Comentário Fechamento:</label><?php echo $row['comentario_fechamenrto'] ?></span>
                                </div>
                                <div class="<?php if(!empty($row["data_fechamento"])) {echo "info-os"; } else {echo"numero_os"; }?>">
                                    <span><label class="titulos_os">Tempo em Aberto:</label> <br><?php if(!empty($row["data_fechamento"]))
                                                                            { 
                                                                                $dataAbertura = new DateTime($row["data_abertura"]);
                                                                                $dataFechamento = new DateTime($row["data_fechamento"]);

                                                                                $intervalo = $dataFechamento->diff($dataAbertura);
                                                                            
                                                                                echo $intervalo->format('%d dias, %H horas, %i minutos');
                                                                            } else {
                                                                                echo "";
                                                                            }?>
                                                                            </span>
                                </div>

                            
                            
                                <div id='' class="<?php if(!empty($row["path"])) {echo "info-os"; } else {echo"numero_os"; }?>">
                                <span class="material-symbols-outlined">attach_file</span>
                                    <a href="<?php echo $row['path'] ?>" target="_blank">Anexos</a>
                                </div>

                            
                              
                                
                        
                </div>
                        <div class="botoes_escolha">
                            <div id="fechar-os-div" onclick="abreFormClose()">Fechar OS</div>
                            <div id="apontamento-os-div" onclick="abreFormApto()">Apontamentos</div>
                        </div>

                        <div id="form-os-close" class="form-fechamento">

                            <form id="fecharos_form" action="" method="POST" name="fechamento">
                                
                            
                                <fieldset >
                                    <legend>Comentário fechamento</legend>
                                    <textarea id=""  class="text_area_close" name="comentario" rows="4" cols="40" required></textarea>

                                </fieldset>
                                
                                <input class="button_finaliza_os" type="submit" value="Finalizar Ordem de Serviço" name="nome" onclick="confirmaFechaOS()">
                            </form>
                        </div>

                        
                        <div id="apontamentos-paradas" class="apontamentos-paradas">                                                         
                            <div id="" class="form-os-apto">     

                                                                                  

                                  <!-- FORM INICIA APONTAMENTO-->  
                                  <button id="button_inicia_ap" onclick="abrirDivApto()" style="<?php if($apto_ini == true && empty($row['data_fechamento'])){echo"display:flex;";}else{echo"display:none;";}?>">Iniciar Apontamento</button> 
                                  
                                  <div id="iniciar_apt_button_div" style="display:none;">
                                    
                                    <p id="alert" >Tem certeza que deseja iniciar o apontamento nessa ordem de serviço?</p>
                                    <button id="cancel" onclick="cancelaAbertura()">Cancelar</button>
                                                                        
                                    <form id="iniciar_ap" action="" method="POST" name="inicia_apontamento">
                                    
                                            
                                            <input id="input_button_ini_ap"  type="submit" value="Iniciar Apontamento" name="nome">
                                    </form>
                                </div>

                                  
                                      

                                   

                            
                                 
                                

                                   
                                
                            </div>                                                    
                                    <!-- INICIO APONTAMENTO -->

                            <?php foreach ($results_apto as $row_apto) { ?>
                                 

                                <div class="table-apto";>

                            

                                    <span id="span_name_prest"><?php echo $row_apto['nome_prestador']?></span>
                                    <br>
                                    <div class="table-apto-ini">
                                        <caption>Início Apontamento</caption>
                                        <table border="1" class="ini_apont">
                                            <tr>
                                                
                                                <td class="td_apont_tit" >Data Início</td>
                                                <td class="td_apont_tit">Hora Início</td>
                                            </tr>
                                            
                                            <tr>
                                                <td  class="td_apont"><?php if(!empty($row_apto["data_inicio"]))
                                                                                {echo date('d/m/Y', strtotime($row_apto["data_inicio"])); } else {echo""; }?></td>
                                                <td  class="td_apont"><?php if(!empty($row_apto["data_inicio"]))
                                                                                {echo date('H:i', strtotime($row_apto["data_inicio"])); } else {echo""; }?></td>
                                                
                                            </tr>
                                            
                                        </table>
                                    </div>

                                    <form class="apontamento_geral" action="" method="POST" name="finaliza_apontamento">
                                        
                                            <div class="table-apto-ini" style="<?php if(empty($row_apto['data_fim'])){echo"display:none;";}else{echo"display:flex;";} ?>">
                                            
                                                <caption>Fim Apontamento</caption>
                                                <table border="1" class="ini_apont">
                                                    <tr>
                                                        
                                                        <td class="td_apont_tit" >Data Fim</td>
                                                        <td class="td_apont_tit" >Hora Fim</td>
                                                    </tr>
                                                    
                                                    <tr class="">
                                                        <td class="td_apont_fec" > <?php if(!empty($row_apto["data_fim"]))
                                                                                        {echo date('d/m/Y', strtotime($row_apto["data_fim"])); } else {echo"-"; }?></td>
                                                        <td  class="td_apont_fec" > <?php if(!empty($row_apto["data_fim"]))
                                                                                        {echo date('H:i', strtotime($row_apto["data_fim"])); } else {echo""; }?></td>
                                                    </tr>
                                                    
                                                    
                        
                                                    
                                                    
                                                </table>
                                            </div>  

                                            <div class="botoes_fechamento">
                                        
                                                <span id="finaliza_apt_button"  style="<?php if(empty($row_apto["data_fim"]) && $parada_ini == true){echo"display:flex;";}else{echo"display:none;";}?>" > <input class="button_finaliza" type="button" value="Finalizar" onclick="finalizarApt()"> </span>
                                                
                                                <span  id="text_finaliza_apt" style="display:none;"> <input id="obs_fechamento" type="text" name="obs_fecha_app" placeholder="Obs (opcional)"> </span>

                                                <span   id="submit_finaliza_apt" style="display:none;"> <input id="finalizar_fechamento" type="submit" value="Finalizar Apontamento" name="nome"> </span>     

                                                <span id="obs_prest" style="<?php if(!empty($row_apto["obs_prestador"])){echo"display:flex;";}else{echo"display:none;";}?>"><p><strong>Obs Fechamento: </strong><?php echo $row_apto['obs_prestador']?></p></span>
                                            </div> 
                                           
                                    </form>
                                    
                                   
                                   
                                        
                                
                                      


                            <!-- PARADA APONTAMENTO -->
                                    <?php foreach ($results_parada as $row) {?>
                                         
                                        <form action="" method="POST" name="finaliza_parada_apontamento">
                                        
                                                
                                                    <div class="table-parada-ini">

                                                        <caption>Parada Apontamento</caption>
                                                        <table border="1" id="table_parada">
                                                            <tr>
                                                                
                                                                <td>Inicio Parada</td>
                                                                <td>Hora</td>
                                                                <td>Fim Parada</td>
                                                                <td>Hora</td>
                                                                <td>Motivo</td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td><?php if(!empty($row["data_inicio_parada"]))
                                                                                                {echo date('d/m/Y', strtotime($row["data_inicio_parada"])); } else {echo"-";}?></td>
                                                                <td><?php if(!empty($row["data_inicio_parada"]))
                                                                                                {echo date('H:i', strtotime($row["data_inicio_parada"])); } else {echo"-"; }?></td>
                                                                <td><?php if(!empty($row["data_fim_parada"]))
                                                                                                {echo date('d/m/Y', strtotime($row["data_fim_parada"])); } else {echo"-"; }?></td>
                                                                <td><?php if(!empty($row["data_fim_parada"]))
                                                                                                {echo date('H:i', strtotime($row["data_fim_parada"])); } else {echo"-"; }?></td>
                                                                <td id="motivoo"><p> <?php echo $row['motivo_parada']?> </p></td>

                                                                <td style="display:none;"><input type="text" value="<?php echo $row['id']?>" name="id_apt_parada"></td>

                                                                <td style="<?php if(empty($row["data_fim_parada"])){echo"display:flex;";}else{echo"display:none;";}?>"><input id="submit_parada" type="submit" value="Retornar Apto" name="nome">
                                                    </td>
                                                                
                                                            </tr>
                                                            
                                                        </table>
                                                    </div>
                                                
                                                
                                        </form>
                                    <?php }?>
                                    

                                </div>
                                 <!-- FORM INICIO PARADA APONTAMENTO -->

                                 <button id="inicia_parada_button" onclick="iniciarParada()" style="<?php if($parada_ini == true && $os_aberta == true && $apto_fim == false){echo"display:flex;";}else{echo"display:none;";}?>">Iniciar Parada</button>
                                 <div id="div_inicia_parada" style="display:none;">
                                    
                                    <p><strong>Tem certeza que deseja iniciar uma parada?</strong> <br><span class="obs_parada">(Paradas não finalizam o apontamento, apenas o suspende por tempo determinado, até que se feche a parada e continue a apontar nessa OS.)</span></p>
                                    
                                    <button onclick="cancelaParada()">Cancelar</button>

                                    <form id="" action="" method="POST" name="inicia_parada_apontamento" >
                                        <input id="botao_parada" type="submit" value="Iniciar Parada Apontamento" name="nome">
                                        <input type="text" id="obs_parada"  name="ocorrencia" rows="4" cols="30" placeholder="Motivo parada (obrigatorio)" required></input>
                                        
                                    </form>
                                </div>
                                <?php }; ?> 
                                <?php }?>
                        </div>


                
                    
            
            
            </div>
                      
                  
                    
              


                


            
                

            
            </main>
        </body>

</html>