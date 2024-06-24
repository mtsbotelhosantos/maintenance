<?php

require ('valida_sessao.php');
include("../../conexao.php");
include('sidebar.php');


    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
   
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];


    // SQL PARA TRAZER AS OS
    $sql_code = "SELECT os_corretiva.id,
    equipamentos.nome_equip,
    os_corretiva.data_abertura,
    os_corretiva.data_fechamento,
    os_corretiva.data_programada,
    SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
    
    FROM os_corretiva

    JOIN equipamentos ON os_corretiva.id_equipamento = equipamentos.id

    WHERE data_programada IS NOT NULL

    ORDER BY os_corretiva.data_programada ASC;"; 

    
    $sql_code = $conn->prepare($sql_code);
    $sql_code->execute();
    $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 
    

    //FILTRAR ABERTAS

    $filtro = "todos"; //tem que estar predefinido
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Verificar se a opção de filtro é selecionada
        if (isset($_GET["filtro_abertas"])) {
            $filtro = $_GET["filtro_abertas"];
            
          
                }
            
        
    }

    



?>

<head>
    <link rel="stylesheet" href="../css/style_plano_manutencao.css">
    
</head>
<main>
    <div class="header">
        <h2 class="title_ord">Planos de Manutenção</h2>

        <span class="botao_abrir_os">
            <a href="abrir_plano_manutencao.php">+ Novo Plano de Manutenção</a>
        </span>
        
        <form method="GET" action="">
            <div class="filtros">
                <label for="">Abertas/Fechadas/Prog</label>
                <select name="filtro_abertas">
                    <option value="todos">[Tudo]</option>
                    <option value="aberta">OS Abertas</option>
                    <option value="fechada">OS Fechadas</option>
                    <option value="programada">OS Programadas</option>
                    
                </select>
       
                <input type="submit" value="Filtrar">
           
            </div>
       </form>
       
    </div>
  
  
    <div class="cards-wrap">

   

        <?php foreach ($results as $row) { 

         
            if($agora_liquido >= date('Y/m/d H:i:s', strtotime($row["data_programada"])) && empty($row["data_fechamento"])){
                $situacao_os = 1;
            }elseif($agora_liquido < date('Y/m/d H:i:s', strtotime($row["data_programada"])) && empty($row["data_fechamento"])){
                $situacao_os = 2;
            }elseif(!empty($row["data_fechamento"])){
                $situacao_os = 0;
            }

                $id = $row['id'];
                if (($filtro == "aberta" && empty($row["data_fechamento"])) && $agora_liquido >= date('Y/m/d H:i:s', strtotime($row["data_programada"]))|| ($filtro == "fechada" && !empty($row["data_fechamento"])) || ($filtro == "programada" && $agora_liquido < date('Y/m/d H:i:s', strtotime($row["data_programada"]))) && empty($row["data_fechamento"]) ||$filtro == "todos") {
                            ?>
                        
            <a class="link-card" href="plano_manutencao_edicao.php?id=<?php echo $id ?>">
                <div class="cards-on">
             
                   

                    <div  class="<?php if(!empty($row["data_fechamento"])) {echo "numero_os_close"; } elseif($agora_liquido < date('Y/m/d H:i:s', strtotime($row["data_programada"]))) {echo"numero_os_prog";} elseif($agora_liquido >= date('Y/m/d H:i:s', strtotime($row["data_programada"]))) {echo"numero_os";}?>"><label>OS: &nbsp; </label> <?php echo $row["id"] ?>  </div>
                    <br>


                    <h2 class="nome_equip"><?php echo $row["nome_equip"] ?></h2>
                    <br>

                    <label>Dt Criação: </label> <?php echo date('d-m-Y H:i', strtotime($row["data_abertura"])) ?>
                    <br>
                    
                    
                    <label>Dt Programada: </label> <?php if(!empty($row["data_programada"]))
                                                            {echo date('d-m-Y H:i', strtotime($row["data_programada"])); } else {echo"-"; } ?>
                    <br>
                   
                    <label>Ocorrencia: </label> <?php echo $row["ocorrencia"] ?> 
                    
                </div>
            </a>
       
            
        <?php }
        } ?>

    </div>
   

       
        

</main>
    
    
