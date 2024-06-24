<?php

require ('valida_sessao.php');
include("../../conexao.php");
include('sidebar.php');


    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
   
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];

    // SQL PARA TRAZER OS PLANOS
    $sql_code = "SELECT planos_manutencao.id,
    equipamentos.nome_equip,
    planos_manutencao.data_inicio_exec,
    planos_manutencao.periodicidade,
    planos_manutencao.servicos,
    planos_manutencao.id_colab_execucao,
    planos_manutencao.id_criador,
    planos_manutencao.data_criacao,
    COLAB.nome AS COLAB,
    CRIADOR.nome AS CRIADOR
    
    FROM planos_manutencao

    JOIN equipamentos ON planos_manutencao.id_equipamento = equipamentos.id
    LEFT JOIN usuarios AS COLAB ON planos_manutencao.id_colab_execucao = COLAB.id
    LEFT JOIN usuarios AS CRIADOR ON planos_manutencao.id_criador = CRIADOR.id

    ORDER BY planos_manutencao.data_inicio_exec ASC;";

    $sql_code = $conn->prepare($sql_code);
    $sql_code->execute();
    $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 



    // SQL PARA TRAZER APENAS AS PROGRAMADAS PRONTAS
    $sql_code_pronto = "SELECT planos_manutencao.id,
    equipamentos.nome_equip,
    equipamentos.id AS ID_EQUIP,
    planos_manutencao.data_inicio_exec,
    planos_manutencao.periodicidade,
    planos_manutencao.servicos,
    planos_manutencao.id_colab_execucao,
    planos_manutencao.id_criador,
    planos_manutencao.data_criacao,
    COLAB.nome AS COLAB,
    COLAB.id AS COLAB_ID,
    CRIADOR.nome AS CRIADOR,
    CRIADOR.id AS CRIADOR_ID
    
    FROM planos_manutencao

    JOIN equipamentos ON planos_manutencao.id_equipamento = equipamentos.id
    LEFT JOIN usuarios AS COLAB ON planos_manutencao.id_colab_execucao = COLAB.id
    LEFT JOIN usuarios AS CRIADOR ON planos_manutencao.id_criador = CRIADOR.id

    WHERE planos_manutencao.data_inicio_exec <= NOW()

    ORDER BY planos_manutencao.data_inicio_exec DESC;";

    $sql_code_pronto = $conn->prepare($sql_code_pronto);
    $sql_code_pronto->execute();
    $results_pronto = $sql_code_pronto->fetchAll(PDO::FETCH_ASSOC); 




        //FOREACH PARA GERAR AS ORDENS PROGRAMADAS

        foreach($results_pronto as $planos_gerar){ 

                $id_equip = $planos_gerar['ID_EQUIP'];
                $id_user_abertura = $planos_gerar['CRIADOR_ID'];
                $id_colab = $planos_gerar['COLAB_ID'];
                $ocorrencia = $planos_gerar['servicos'];
                $id_plano = $planos_gerar['id'];
        

                $data_execucao = $planos_gerar["data_inicio_exec"];
                $data_atual = $agora_liquido;
                $dias_a_adicionar = $planos_gerar["periodicidade"];
                $id_prog = $planos_gerar["id"];

                    
                
                        if($data_execucao <= $data_atual){
                        
                            //UPDATE DA PROX DATA DE EXECUÇÃO
                            $data_execucao_nova = date('Y/m/d H:i:s', strtotime("$data_execucao + $dias_a_adicionar days"));
                               $sql_form = "UPDATE planos_manutencao SET data_inicio_exec = :data_inicio_exec
                                    WHERE id = :id;";

                                $sql_form = $conn->prepare($sql_form);
                                $sql_form->bindParam(':data_inicio_exec', $data_execucao_nova);
                                $sql_form->bindParam(':id', $id_prog);
                                $sql_form->execute();


                            //INSERT OS PROGRAMADA NA TABELA DE ORDENS 
                                $data_abertura_edit = date('Y/m/d H:i:s', strtotime("$data_execucao"));
                                $data_prev_fecha_edit = date('Y/m/d H:i:s', strtotime("$data_execucao + 1 days"));

                                $sql_form = "INSERT INTO os_corretiva (data_abertura, id_equipamento, id_user_abertura, id_colab_execucao, ocorrencia, data_prev_fecha, id_plano_preventiva) VALUES (:data_abertura, :id_equipamento, :id_user_abertura, :id_colab_execucao, :ocorrencia, :data_prev_fecha, :id_plano)"; 
                                $sql_form = $conn->prepare($sql_form);
                                $sql_form->bindParam(':data_abertura', $data_abertura_edit);
                                $sql_form->bindParam(':id_equipamento', $id_equip);
                                $sql_form->bindParam(':id_user_abertura', $id_user_abertura);
                                $sql_form->bindParam(':id_colab_execucao', $id_colab);
                                $sql_form->bindParam(':ocorrencia', $ocorrencia);
                                $sql_form->bindParam(':data_prev_fecha', $data_prev_fecha_edit);
                                $sql_form->bindParam(':id_plano', $id_plano);

                                $sql_form->execute();

                                
                        



                        }
                
            }
        

        // $data_execucao = $planos_gerar["data_inicio_exec"];
        // $data_atual = $agora_liquido;
        // $dias_a_adicionar = $planos_gerar["periodicidade"];
        // $id_prog = $planos_gerar["id"];

        
        //  if($data_execucao <= $data_atual){

        //     $data_execucao_nova = date($data_execucao, strtotime("+.'$dias_a_adicionar'. days"));
        //     var_dump($data_execucao_nova);
           
        //    $sql_form = "UPDATE planos_manutencao SET data_inicio_exec = :data_inicio_exec
        //         WHERE id = :id;";

        //     $sql_form = $conn->prepare($sql_form);
        //     $sql_form->bindParam(':data_inicio_exec', $_POST['data_prev']);
        //     $sql_form->bindParam(':id', $id_prog);
        //     $sql_form->execute();

            

         

    

    

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/style_plano_manutencao_new.css">

        <script defer src="../js/script_equipamentos.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <title>Planos Manutenção</title>
    </head>
    <body>
        

        <main>
            <div class="header">
                <h2 class="title_ord">Planos de Manutenção</h2>

                <span class="botao_abrir_os">
                    <a href="abrir_plano_manutencao.php">+ Novo Plano de Manutenção</a>
                </span>
                
                <!-- <form method="GET" action="">
                    <div class="filtros">
                        <label for="">Filtro Periodicidade</label>
                        <select name="filtro_abertas">
                            <option value="todos">[Tudo]</option>
                            <option value="15">Quinzenal</option>
                            <option value="30">Mensal</option>
                            <option value="90">Trimestral</option>
                            <option value="180">Semestral</option>
                            
                        </select>
            
                        <input type="submit" value="Filtrar">
                
                    </div>
            </form> -->
            
            </div>

            <hr>

    
        
        <div class="card_wraper">
            <table>
                <thead>
                    <tr>
                        <th class="sortable">ID Plano</th>
                        <th class="sortable">Equipamento</th>
                        <th class="sortable">Data Prox Exec</th>
                        <th class="sortable">Periodicidade</th>
                        <th class="sortable">Colab Exec</th>
                        <th class="sortable">Criador</th>
                        <th class="sortable">Data Criação</th>
                        <th class="sortable">Serviços</th>
                    </tr>
                </thead>
            <?php foreach($results as $plano){ ?>
                
                
                <tbody>
               
                    <tr>
                        <td><?php echo($plano['id']); ?></td>
                        <td><?php echo($plano['nome_equip']); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($plano["data_inicio_exec"])) ?></td>
                        <td><?php echo($plano['periodicidade']); ?> dias</td>
                        <td><?php echo($plano['COLAB']); ?></td>
                        <td><?php echo($plano['CRIADOR']); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($plano["data_criacao"])) ?></td>
                        <td><?php echo($plano['servicos']); ?></td>
                    
            
                        
                    </tr>
                  
                    
                    
                    
                    
                </tbody>
               
                
            <?php } ?>
            </table>
            

            
        </div>
        

        </main>
    </body>
</html>