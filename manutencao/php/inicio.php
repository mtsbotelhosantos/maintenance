<?php
require ('valida_sessao.php');
include("../../conexao.php");
include('sidebar.php');

       date_default_timezone_set('America/Sao_Paulo');
       $agora = new DateTime('now');
       $agora_liquido = $agora->format('Y/m/d H:i:s');
       $usuario = $_SESSION['id_session'];


       // SQL PARA TRAZER AS OS (EM ABERTO AGUARDANDO)
              $sql_code = "SELECT DISTINCT
              os_corretiva.id,
              equipamentos.nome_equip,
              os_corretiva.data_abertura,
              os_corretiva.data_fechamento,
              os_corretiva.data_programada,
              os_corretiva.data_prev_fecha,
              os_corretiva.id_plano_preventiva,
              SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
              FROM
                     os_corretiva
              JOIN
                     equipamentos ON os_corretiva.id_equipamento = equipamentos.id
              LEFT JOIN
                     apontamentos ON os_corretiva.id = apontamentos.id_os
              WHERE
                     apontamentos.id_os IS NULL
              ORDER BY
                     os_corretiva.data_prev_fecha ASC;"; 


              $sql_code = $conn->prepare($sql_code);
              $sql_code->execute();
              $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 




       // SQL PARA TRAZER AS OS (Execuçao)
              $sql_code = "SELECT DISTINCT
              os_corretiva.id,
              equipamentos.nome_equip,
              os_corretiva.data_abertura,
              os_corretiva.data_fechamento,
              os_corretiva.data_programada,
              os_corretiva.data_prev_fecha,
              os_corretiva.id_plano_preventiva,
              SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
              FROM
                     os_corretiva
              JOIN
                     equipamentos ON os_corretiva.id_equipamento = equipamentos.id
              JOIN
                     apontamentos ON os_corretiva.id = apontamentos.id_os
              LEFT JOIN
                     paradas ON os_corretiva.id = paradas.id_os
              WHERE
                     (apontamentos.data_fim IS NULL)
                     AND (paradas.data_fim_parada IS NOT NULL OR paradas.id_os IS NULL)
                     AND NOT EXISTS (
                     SELECT 1
                     FROM paradas p
                     WHERE os_corretiva.id = p.id_os
                     AND p.data_fim_parada IS NULL
                     )
              ORDER BY
                     os_corretiva.data_prev_fecha ASC;
              "; 


              $sql_code = $conn->prepare($sql_code);
              $sql_code->execute();
              $results_exec = $sql_code->fetchAll(PDO::FETCH_ASSOC); 

       // SQL PARA TRAZER AS OS (Suspensa)
              $sql_code = "SELECT DISTINCT
              os_corretiva.id,
              equipamentos.nome_equip,
              os_corretiva.data_abertura,
              os_corretiva.data_fechamento,
              os_corretiva.data_programada,
              os_corretiva.data_prev_fecha,
              os_corretiva.id_plano_preventiva,
              SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
              FROM
                     os_corretiva
              JOIN
                     equipamentos ON os_corretiva.id_equipamento = equipamentos.id
              JOIN
                     paradas ON os_corretiva.id = paradas.id_os
              
              WHERE paradas.data_fim_parada IS NULL
              ORDER BY
                     os_corretiva.data_prev_fecha ASC;"; 


              $sql_code = $conn->prepare($sql_code);
              $sql_code->execute();
              $results_parada = $sql_code->fetchAll(PDO::FETCH_ASSOC);


       // SQL PARA TRAZER AS OS (Aguardando Fechamento)
               $sql_code = "SELECT DISTINCT
               os_corretiva.id,
               equipamentos.nome_equip,
               os_corretiva.data_abertura,
               os_corretiva.data_fechamento,
               os_corretiva.data_programada,
               os_corretiva.data_prev_fecha,
               os_corretiva.id_plano_preventiva,
               SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
               FROM
                      os_corretiva
               JOIN
                      equipamentos ON os_corretiva.id_equipamento = equipamentos.id
               JOIN
                      paradas ON os_corretiva.id = paradas.id_os
               
               JOIN
                      apontamentos ON os_corretiva.id = apontamentos.id_os
               WHERE  NOT EXISTS (
                      SELECT 1
                      FROM apontamentos p
                      WHERE os_corretiva.id = p.id_os
                      AND p.data_fim IS NULL
                      )
 
                      AND NOT EXISTS (
                      SELECT 1
                      FROM paradas p
                      WHERE os_corretiva.id = p.id_os
                      AND p.data_fim_parada IS NULL
                      )
                      AND os_corretiva.data_fechamento IS NULL
               ORDER BY
                      os_corretiva.data_prev_fecha ASC;"; 

              $sql_code = $conn->prepare($sql_code);
              $sql_code->execute();
              $results_ag_fecha = $sql_code->fetchAll(PDO::FETCH_ASSOC);

       // SQL PARA TRAZER AS OS (Finalizada)
              $sql_code = "SELECT DISTINCT
              os_corretiva.id,
              equipamentos.nome_equip,
              os_corretiva.data_abertura,
              os_corretiva.data_fechamento,
              os_corretiva.data_programada,
              os_corretiva.data_prev_fecha,
              os_corretiva.id_plano_preventiva,
              SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
              FROM
                     os_corretiva

              JOIN
                      equipamentos ON os_corretiva.id_equipamento = equipamentos.id
              
              WHERE os_corretiva.data_fechamento IS NOT NULL 
              ORDER BY
                     os_corretiva.data_prev_fecha ASC;
              "; 

              $sql_code = $conn->prepare($sql_code);
              $sql_code->execute();
              $results_fecha = $sql_code->fetchAll(PDO::FETCH_ASSOC);


              
              //FILTRAR ABERTAS

              $filtro = "todos"; //tem que estar predefinido
              if ($_SERVER["REQUEST_METHOD"] == "GET") {
                     // Verificar se a opção de filtro é selecionada
                     if (isset($_GET["filtro_abertas"])) {
                     $filtro = $_GET["filtro_abertas"];
            
          
                }
            
        
    }


?>
<!DOCTYPE html>
<html lang="pt-br">
       <head>
               <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <link rel="stylesheet" href="../css/inicio.css">
              <!-- <script defer src="../js/script_inicio.js"></script> -->
              <title>Início</title>
       </head>
       <body>
              <main>
                     <div class="header">
                            <h2 class="title_ord">Situação das Ordens de Serviço</h2>
                            <form method="GET" action="">
                                   <div class="filtros">
                                          <label for="">Corretivas/Programadas</label>
                                          <select name="filtro_abertas">
                                          <option value="todos">[Tudo]</option>
                                          <option value="corretiva">OS Corretivas</option>
                                          <option value="programada">OS Programadas</option>
                                          
                                          </select>
                                   
                                          <input type="submit" value="Filtrar">
                                   
                                   </div>
                            </form>
                     </div>
                    
                     <div class="central">
                            <div class="em_aberto">
                                   <div id="em_aberto_tit" class="tit_cards" onclick="teste()">
                                          <h3 class="">Em Aberto (aguardando)</h3>
                                          
                                          <span class="teste_span">Ordens de Serviço que não foram iniciados os apontamentos</span>
                                   </div>
                                   <?php foreach($results as $row){ 
                                          $id = $row['id']; 

                                          


                                          if (($filtro == "corretiva" && empty($row["id_plano_preventiva"])) || ($filtro == "programada" && !empty($row["id_plano_preventiva"])) || $filtro == "todos") {
                                                 ?>

                                          <a class="link-card" href="<?php if(empty($row["data_programada"])){ echo"ordens_corretivas_edicao.php?id=";?><?php echo $id;}  elseif(!empty($row["data_programada"])){ echo"plano_manutencao_edicao.php?id=";?><?php echo $id; }?>">
                                                 <div class="<?php if(empty($row["id_plano_preventiva"])){echo"cards-on-corretiva";}else{echo"cards-on-preventiva";} ?>">
                                                 

                                                        <div  class="em_aberto_n"><label>OS: &nbsp; </label> <?php echo $row["id"] ?> </div>
                                                        <br>

                                                        <h2 class="nome_equip"><?php echo $row["nome_equip"] ?></h2>
                                                        <br>

                                                        <label>Dt Abertura: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?>
                                                        <br>
                                                        

                                                        
                                                        <label>Dt Prev Final: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_prev_fecha"]));?>
                                                        <br>
                                                        <label>Ocorrencia: </label> <?php echo $row["ocorrencia"] ?> 
                                                 
                                                 </div>
                                          </a>

                                                
                                   <?php }} ?>

                            </div>
                            
                            <div class="em_exec">
                                   <div id="em_exec_tit" class="tit_cards">
                                          <h3>Execução</h3>
                                          <span>Ordens de Serviço que estão com apontamentos em aberto</span>
                                  
                                   </div>
                                   <?php foreach($results_exec as $row){ 
                                          $id = $row['id']; 

                                                 if (($filtro == "aberta" && empty($row["data_programada"])) || ($filtro == "programada" && !empty($row["data_programada"]))|| $filtro == "todos") {
                                                 ?>
                                          <a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo $id ?>">
                                          <div class="<?php if(empty($row["id_plano_preventiva"])){echo"cards-on-corretiva";}else{echo"cards-on-preventiva";} ?>">
                                                 

                                                        <div  class="numero_os"><label>OS: &nbsp; </label> <?php echo $row["id"] ?> </div>
                                                        <br>

                                                        <h2 class="nome_equip"><?php echo $row["nome_equip"] ?></h2>
                                                        <br>

                                                        <label>Dt Abertura: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?>
                                                        <br>
                                                        
                                                        <label>Dt Prev Final: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_prev_fecha"]));?>
                                                        <br>
                                                        <label>Ocorrencia: </label> <?php echo $row["ocorrencia"] ?> 
                                                 
                                                 </div>
                                          </a>


                                   <?php }} ?>

                            </div>

                            <div class="paradas">
                                    <div id="paradas_tit" class="tit_cards">
                                          <h3>Suspensas</h3>
                                   </div>
                                   <?php foreach($results_parada as $row){ 
                                          $id = $row['id']; 

                                                 if (($filtro == "aberta" && empty($row["data_programada"])) || ($filtro == "programada" && !empty($row["data_programada"]))|| $filtro == "todos") {
                                                 ?>

                                          <a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo $id ?>">
                                                 <div class="<?php if(empty($row["id_plano_preventiva"])){echo"cards-on-corretiva";}else{echo"cards-on-preventiva";} ?>">
                                                 

                                                        <div  class="paradas_n"><label>OS: &nbsp; </label> <?php echo $row["id"] ?> </div>
                                                        <br>

                                                        <h2 class="nome_equip"><?php echo $row["nome_equip"] ?></h2>
                                                        <br>

                                                        <label>Dt Abertura: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?>
                                                        <br>
                                                        
                                                        <label>Dt Prev Final: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_prev_fecha"]));?>
                                                        <br>
                                                        <label>Ocorrencia: </label> <?php echo $row["ocorrencia"] ?> 
                                                 
                                                 </div>
                                          </a>


                                   <?php }} ?>

                            </div>

                            <div class="ag_fecha">
                                   <div id="ag_fecha_tit" class="tit_cards">
                                          <h3>Aguardando Fechamento</h3>
                                   </div>
                                   <?php foreach($results_ag_fecha as $row){ 
                                          $id = $row['id']; 

                                                 if (($filtro == "aberta" && empty($row["data_programada"])) || ($filtro == "programada" && !empty($row["data_programada"]))|| $filtro == "todos") {
                                                 ?>
                                          <a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo $id ?>">
                                                 <div class="<?php if(empty($row["id_plano_preventiva"])){echo"cards-on-corretiva";}else{echo"cards-on-preventiva";} ?>">
                                                 

                                                        <div  class="ag_fecha_n"><label>OS: &nbsp; </label> <?php echo $row["id"] ?> </div>
                                                        <br>

                                                        <h2 class="nome_equip"><?php echo $row["nome_equip"] ?></h2>
                                                        <br>

                                                        <label>Dt Abertura: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?>
                                                        <br>
                                                        
                                                        <label>Dt Prev Final: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_prev_fecha"]));?>
                                                        <br>
                                                        <label>Ocorrencia: </label> <?php echo $row["ocorrencia"] ?> 
                                                 
                                                 </div>
                                          </a>


                                   <?php }} ?>

                            </div>

                            <div class="fechadas">
                                   <div id="finalizadas_tit" class="tit_cards">
                                          <h3>Finalizadas</h3>
                                   </div>
                                   <?php foreach($results_fecha as $row){ 
                                          $id = $row['id']; 

                                                 if (($filtro == "aberta" && empty($row["data_programada"])) || ($filtro == "programada" && !empty($row["data_programada"]))|| $filtro == "todos") {
                                                 ?>
                                          <a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo $id ?>">
                                                 <div class="<?php if(empty($row["id_plano_preventiva"])){echo"cards-on-corretiva";}else{echo"cards-on-preventiva";} ?>">
                                                 

                                                        <div  class="numero_os_close"><label>OS: &nbsp; </label> <?php echo $row["id"] ?> </div>
                                                        <br>

                                                        <h2 class="nome_equip"><?php echo $row["nome_equip"] ?></h2>
                                                        <br>

                                                        <label>Dt Abertura: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?>
                                                        <br>
                                                        
                                                        <label>Dt Prev Final: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_prev_fecha"]));?>
                                                        <br>
                                                        <label>Ocorrencia: </label> <?php echo $row["ocorrencia"] ?> 
                                                 
                                                 </div>
                                          </a>


                                   <?php }} ?>

                            </div>

                     </div>
                     
                   
                     


              </main>
       </body>

</html>
            
        
         
            