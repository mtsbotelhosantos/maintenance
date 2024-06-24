<?php

require ('valida_sessao.php');
include("../../conexao.php");
include('sidebar.php');


    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];
    $results = [];

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(!empty($dados)){

    

        if(!empty($dados['data_ini']) && !empty($dados['data_fim'])){

            $sql_form = "SELECT 
            usuarios.nome AS nome_usuario,
            apontamentos.id_os,
            apontamentos.data_inicio AS inicio_apontamento,
            MIN(paradas.data_inicio_parada) AS inicio_parada,
            MAX(paradas.data_fim_parada) AS fim_parada,
            apontamentos.data_fim AS fim_apontamento,
            TIMEDIFF(apontamentos.data_fim, apontamentos.data_inicio) AS tempo_total_apontado,
            TIMEDIFF(paradas.data_fim_parada, paradas.data_inicio_parada) AS tempo_total_parado,
            TIMEDIFF(
                TIMEDIFF(apontamentos.data_fim, apontamentos.data_inicio),
                SUM(TIMEDIFF(paradas.data_fim_parada, paradas.data_inicio_parada))
            ) AS Total_Liquido
        FROM 
            apontamentos
        JOIN 
            usuarios ON apontamentos.id_prestador = usuarios.id
        LEFT JOIN 
            paradas ON apontamentos.id_os = paradas.id_os AND apontamentos.id_prestador = paradas.id_prestador
        WHERE
            apontamentos.data_inicio >= :data_ini AND apontamentos.data_fim <= :data_fim
        GROUP BY 
            apontamentos.id_os, apontamentos.id_prestador, apontamentos.data_inicio, apontamentos.data_fim
        ORDER BY 
            apontamentos.id_os;
        ";

                $sql_form = $conn->prepare($sql_form);

                $sql_form->bindParam(':data_ini', $_POST['data_ini']); 
                $sql_form->bindParam(':data_fim', $_POST['data_fim']);
                                    
                    

                $sql_form->execute();
                $results = $sql_form->fetchAll(PDO::FETCH_ASSOC); 

                
        
            }else{
                ?><script> alert("Data Inicio e Data Fim são obrigatórias!");</script>
                <?php 

            }
               
    }




?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_relatorios.css">
    <title>Relatórios</title>
</head>
<body>
    <main>
            <h2>Relatório Horas Trabalhadas</h2>
        <form  method="POST" id="abriros_form" action="">
           
                


                  <fieldset>
                    <legend>Filtrar Período</legend>

                    <legend class="legend_datas">Data Início</legend>
                    <input type="datetime-local" name="data_ini" class="data_prog" value="<?php if(!empty($dados['data_ini'])){echo date('Y-m-d\TH:i:s', strtotime($dados['data_ini']));} ?>">

                   
                    <legend class="legend_datas">Data Fim</legend>
                    <input type="datetime-local" name="data_fim" class="data_prog" value="<?php if(!empty($dados['data_fim'])){echo date('Y-m-d\TH:i:s', strtotime($dados['data_fim']));} ?>">

                    <input type="submit" id="abrir" value="Buscar">

                  

                  </fieldset>
                  



                            <?php
            // Agrupa os resultados por nome de usuário
            $resultados_por_nome = [];
            foreach ($results as $row) {
                $nome_usuario = $row['nome_usuario'];
                if (!isset($resultados_por_nome[$nome_usuario])) {
                    $resultados_por_nome[$nome_usuario] = [];
                }
                $resultados_por_nome[$nome_usuario][] = $row;
            }
            ?>

            <?php foreach ($resultados_por_nome as $nome_usuario => $resultados_nome) { ?>
                <h2 class="titulo_nomes"><?php echo $nome_usuario; ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">Nome</th>
                            <th class="sortable">OS</th>
                            <th class="sortable">Inicio Apto</th>
                            <th class="sortable">Fim Apto</th>
                            <th class="sortable">Total Apontado</th>
                            <th class="sortable">Inicio Parada</th>
                            <th class="sortable">Fim Parada</th>
                            <th class="sortable">Total Parada</th>
                            <th class="sortable">Total Liquido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_apontado_por_nome = 0; // Total apontado para o nome de usuário atual
                        $total_parada_por_nome = 0; // Total de paradas para o nome de usuário atual
                        $total_liquido_por_nome = 0; // Total líquido para o nome de usuário atual
                        foreach ($resultados_nome as $row) { 
                            // Cálculos de cada linha
                            $dataIniAp = new DateTime($row["inicio_apontamento"]);
                            $dataFimAp = new DateTime($row["fim_apontamento"]);
                            $intervalo_ap = $dataFimAp->diff($dataIniAp);

                            $dataAbertura = new DateTime($row["inicio_parada"]);
                            $dataFechamento = new DateTime($row["fim_parada"]);
                            $intervalo_parada = $dataFechamento->diff($dataAbertura);

                            $total_apontado = $intervalo_ap->days * 24 * 60 + $intervalo_ap->h * 60 + $intervalo_ap->i;
                            $total_parada = $intervalo_parada->days * 24 * 60 + $intervalo_parada->h * 60 + $intervalo_parada->i;

                            // Atualiza os totais por nome de usuário
                            $total_apontado_por_nome += $total_apontado;
                            $total_parada_por_nome += $total_parada;
                            $total_liquido_por_nome += $total_apontado - $total_parada;
                        ?>
                            <tr>
                                <!-- Conteúdo da linha -->
                                <td><?php echo($row['nome_usuario']); ?></td>
                                <td><a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo($row['id_os']) ?>" target="_blank"> <?php echo($row['id_os']); ?> </a></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row["inicio_apontamento"])) ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row["fim_apontamento"])) ?></td>
                                <td class="total_apontado"><?php echo $intervalo_ap->format('%d dias, %H horas, %i min'); ?></td>
                                <td><?php echo $row["inicio_parada"] != NULL ? date('d-m-Y H:i', strtotime($row["inicio_parada"])) : ""; ?></td>
                                <td><?php echo $row["fim_parada"] != NULL ? date('d-m-Y H:i', strtotime($row["fim_parada"])) : ""; ?></td>
                                <td class="total_apontado"><?php echo $intervalo_parada->format('%d dias, %H horas, %i min'); ?></td>
                                <td class="total_liquido"><?php echo floor(($total_apontado - $total_parada) / (24 * 60)) . " dias, " . floor((($total_apontado - $total_parada) % (24 * 60)) / 60) . " horas, " . (($total_apontado - $total_parada) % 60) . " min"; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="resumo"><?php echo floor($total_apontado_por_nome / (24 * 60)) . " dias, " . floor(($total_apontado_por_nome % (24 * 60)) / 60) . " horas, " . ($total_apontado_por_nome % 60) . " min"; ?></td>
                            <td></td>
                            <td></td>
                            <td class="resumo"><?php echo floor($total_parada_por_nome / (24 * 60)) . " dias, " . floor(($total_parada_por_nome % (24 * 60)) / 60) . " horas, " . ($total_parada_por_nome % 60) . " min"; ?></td>
                            <td class="resumo_total"><?php echo floor($total_liquido_por_nome / (24 * 60)) . " dias, " . floor(($total_liquido_por_nome % (24 * 60)) / 60) . " horas, " . ($total_liquido_por_nome % 60) . " min"; ?></td>
                        </tr>
                    </tfoot>
                </table>
            <?php } ?>





           

                  
           

             

                 



          
                    
                    
                    
        </form>
    </main>

    
</body>
</html>