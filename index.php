<?php 

    if(!empty(filter_input(INPUT_POST, 'btnInput', FILTER_DEFAULT))){

        $valida_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $valida_senha = filter_input(INPUT_POST, 'senha', FILTER_DEFAULT);

        

        if($valida_email){


            include("conexao.php");

            $sql_code = "SELECT nome, email, senha, id FROM usuarios where email = :email LIMIT 1"; 
            
           
            $sql_code = $conn->prepare($sql_code);

            

            $sql_code->bindParam(':email',$valida_email);

            
            $sql_code->execute();

            
            
            $results = $sql_code->fetchAll(PDO::FETCH_ASSOC); //fech se for apenas 1 e fetchAll se for mais de 1
            
           
                
                if(empty($results)){

                    ?> <script> alert("E-mail incorreto!"); </script> <?php
                    header("Refresh: 0");

                }else {

                    foreach ($results as $row) {
                      
            
                        if(($row["email"] == $valida_email) && ($row["senha"] == $valida_senha)){
                           
                            

                            if(!isset($_SESSION)){
                                
                                session_start();
                                
                                $_SESSION['id_session'] = $row["id"];
                                
                                header("Location: manutencao/php/inicio.php");

                            }
                    
                        }else{
                            ?> <script> alert("Senha incorreta!"); </script> <?php
                            header("Refresh: 0");
                        }
                    }
                }
            

            
              

        }else{ ?> <script> alert("E-mail inexistente!"); </script> <?php  
                header("Refresh: 0");
        }
         
        
    }
        


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Entrar</title>
</head>
<body>

    <div class="modal">
        
        <div class="div-image">
            <img src="assets/logo_sobre_nos5.png" alt="gráfica canastra" srcset="" id="logo">
        </div>
        
            <form method="POST" action="">
                
                <input type="email" name="email" id="" placeholder="email@mail.com.br" required>
                <br>
                
                <input type="password" name="senha" id="" placeholder="senha" required>
                <br><br>
                <input type="submit" name="btnInput" value="Entrar" class="button">
            </form>
        
    </div>

   
    
</body>
</html>