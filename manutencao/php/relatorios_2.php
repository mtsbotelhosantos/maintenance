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
            apontamentos.id_os,
            usuarios.nome AS nome_usuario,
            apontamentos.data_inicio AS inicio_apontamento,
            apontamentos.data_fim AS fim_apontamento,
            CONCAT(
                FLOOR(SUM(TIMESTAMPDIFF(SECOND, apontamentos.data_inicio, apontamentos.data_fim)) / (24 * 60 * 60)), ' dias, ',
                FLOOR((SUM(TIMESTAMPDIFF(SECOND, apontamentos.data_inicio, apontamentos.data_fim)) % (24 * 60 * 60)) / (60 * 60)), ' horas, ',
                FLOOR((SUM(TIMESTAMPDIFF(SECOND, apontamentos.data_inicio, apontamentos.data_fim)) % (60 * 60)) / 60), ' min'
            ) AS total_horas_apontadas_formatado,
            IFNULL(
                CONCAT(
                    FLOOR(SUM(paradas.total_paradas) / (24 * 60 * 60)), ' dias, ',
                    FLOOR((SUM(paradas.total_paradas) % (24 * 60 * 60)) / (60 * 60)), ' horas, ',
                    FLOOR((SUM(paradas.total_paradas) % (60 * 60)) / 60), ' min'
                ),
                '0 dias, 0 horas, 0 min'
            ) AS total_horas_paradas_formatado,
            CONCAT(
                FLOOR(
                    (
                        SUM(TIMESTAMPDIFF(SECOND, apontamentos.data_inicio, apontamentos.data_fim))
                        -
                        IFNULL(SUM(paradas.total_paradas), 0)
                    ) / (24 * 60 * 60)
                ), ' dias, ',
                FLOOR(
                    (
                        (
                            SUM(TIMESTAMPDIFF(SECOND, apontamentos.data_inicio, apontamentos.data_fim))
                            -
                            IFNULL(SUM(paradas.total_paradas), 0)
                        ) % (24 * 60 * 60)
                    ) / (60 * 60)
                ), ' horas, ',
                FLOOR(
                    (
                        (
                            SUM(TIMESTAMPDIFF(SECOND, apontamentos.data_inicio, apontamentos.data_fim))
                            -
                            IFNULL(SUM(paradas.total_paradas), 0)
                        ) % (60 * 60)
                    ) / 60
                ), ' min'
            ) AS diferenca_horas
        FROM 
            apontamentos
        LEFT JOIN 
            (
                SELECT 
                    id_os,
                    SUM(TIMESTAMPDIFF(SECOND, data_inicio_parada, data_fim_parada)) AS total_paradas
                FROM 
                    paradas
                GROUP BY 
                    id_os
            ) AS paradas
        ON 
            apontamentos.id_os = paradas.id_os
        LEFT JOIN 
            usuarios
        ON 
            apontamentos.id_prestador = usuarios.id
        WHERE
            apontamentos.data_inicio >= :data_ini  AND apontamentos.data_fim <= :data_fim
        GROUP BY 
            apontamentos.id_os, apontamentos.id_prestador;
        
        
        
        
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

                  
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>OS</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    
                    <th>Horas Trabalhadas</th>
                    <th>Horas Paradas</th>
                    <th>Horas Líquidas</th>
                </tr>
            </thead>

            <?php foreach($results as $row){ ?>

            <tbody>
                <tr>
                    
                    <td><?php echo$row['nome_usuario'] ?></td>
                    <td><a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo($row['id_os']) ?>" target="_blank"> <?php echo($row['id_os']); ?> </a></td>
                                
                    <td><?php echo date('d-m-Y H:i', strtotime($row['inicio_apontamento'])) ?></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($row['fim_apontamento'])) ?></td>
                    <td><?php echo $row['total_horas_apontadas_formatado'] ?></td>
                    <td><?php echo$row['total_horas_paradas_formatado'] ?></td>
                    <td><?php echo$row['diferenca_horas'] ?></td>


                    


                    
                </tr>
            </tbody>

            <?php } ?>

            <!-- ------ -->


        

                
                  



      
    </main>

    
</body>
</html>