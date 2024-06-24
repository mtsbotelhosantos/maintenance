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
    os_corretiva.data_prev_fecha,
    os_corretiva.id_plano_preventiva,
    SUBSTRING(os_corretiva.ocorrencia, 1, 70) AS ocorrencia
    
    FROM os_corretiva

    JOIN equipamentos ON os_corretiva.id_equipamento = equipamentos.id

    

    ORDER BY os_corretiva.data_prev_fecha ASC;"; 

    
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
    <link rel="stylesheet" href="../css/style_ordens_corretivas.css">
    
</head>
<main>
    <div class="header">
        <h2 class="title_ord">Ordens de Serviço</h2>

        <span class="botao_abrir_os">
            <a href="abrir_os.php">+ Nova OS</a>
        </span>
        
        <form method="GET" action="">
            <div class="filtros">
                <label for="">Abertas/Fechadas</label>
                <select name="filtro_abertas">
                    <option value="todos">[Tudo]</option>
                    <option value="aberta">OS Abertas</option>
                    <option value="fechada">OS Fechadas</option>
                    
                </select>
       
                <input type="submit" value="Filtrar">
           
            </div>
       </form>
       
    </div>
  
  
    <div class="cards-wrap">

   

        <?php foreach ($results as $row) { 
            
           

                $id = $row['id'];
                if (($filtro == "aberta" && empty($row["data_fechamento"])) || ($filtro == "fechada" && !empty($row["data_fechamento"])) || $filtro == "todos") {
                            ?>
                        
            <a class="link-card" href="ordens_corretivas_edicao.php?id=<?php echo $id ?>">
                <div class="<?php if(empty($row["id_plano_preventiva"])){echo"cards-on-corretiva";}else{echo"cards-on-preventiva";} ?>">
                  

                    <div  class="<?php if(!empty($row["data_fechamento"])) {echo "numero_os_close"; } elseif(!empty($row["id_plano_preventiva"])){echo"numero_os_prev";} else{echo"numero_os";}?>"><label>OS: &nbsp; </label> <?php echo $row["id"] ?> </div>
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
       
            
        <?php }
        } ?>

    </div>
   

       
        

</main>
    
    
