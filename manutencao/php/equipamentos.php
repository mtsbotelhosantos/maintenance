<?php
require ('valida_sessao.php');
include("../../conexao.php");
include('sidebar.php');

    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime('now');
    $agora_liquido = $agora->format('Y/m/d H:i:s');
    $usuario = $_SESSION['id_session'];


    // SQL PARA TRAZER OS EQUIPAMENTOS
    $sql_code = "SELECT equipamentos.id,
                        nome_equip,
                        id_user_criacao,
                        data_criacao,
                        usuarios.nome as nome 
                        FROM equipamentos
                        JOIN usuarios ON usuarios.id = equipamentos.id_user_criacao;"; 


    $sql_code = $conn->prepare($sql_code);
    $sql_code->execute();
    $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); 

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_equipamentos.css">

    <script defer src="../js/script_equipamentos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Equipamentos</title>
</head>
<body>
    <main>
    <div class="header">
        <h2 class="title_ord">Equipamentos Cadastrados</h2>

        <span class="botao_abrir_os">
            <a href="cadastra_equip.php">+ Equipameto</a>
        </span>
        
       
       
    </div>
    <hr>

    
        
        <div class="card_wraper">
            <table>
                <thead>
                    <tr>
                        <th class="sortable">ID Equip</th>
                        <th class="sortable">Nome</th>
                        <th class="sortable">Usuário Criação</th>
                        <th class="sortable">Data Criação</th>
                    </tr>
                </thead>
            <?php foreach($results as $equip){ ?>
                
                <tbody>
               
                    <tr>
                        <td><?php echo($equip['id']); ?></td>
                        <td><?php echo($equip['nome_equip']); ?></td>
                        <td><?php echo($equip['nome']); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($equip["data_criacao"])) ?></td>
                       
                        
                    </tr>
                    
                    
                    
                </tbody>
                
            <?php } ?>
            </table>
            

            
        </div>

        







    </main>

    
    
</body>
</html>