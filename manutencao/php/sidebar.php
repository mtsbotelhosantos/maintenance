<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_sidebar.css">
    
    
    
    <title>Document</title>
</head>
<body>



    <aside class="aside-web" id="aside-web-id"  onmouseover="asideFunc()" onmouseout="asideFunc()">
        <header onclick="redirect_logo()">
            <img src="../../assets/logo.png" alt="logo_canastra">
        </header>
        <nav>
            <button onclick="redirecionarInicio()">
                <span>
                    <i class="material-symbols-outlined">home</i>
                    <span id="web-text" class="web-text">Inicio</span>
                </span>
            </button>
            <button onclick="redirecionarAbrirOS()">
                <span>
                    <i class="material-symbols-outlined">file_open</i>
                    <span id="web-text2" class="web-text">Ordem de Serviço</span>                  
                </span>
            </button>
            <button onclick="redirecionarPlanManu()">
                <span>
                    <i class="material-symbols-outlined">chronic</i>
                    <span id="web-text3" class="web-text">Plano Manutenção</span>
                </span>
            </button>
            <button onclick="redirecionarEquip()">
                <span>
                    <i class="material-symbols-outlined">switch_access</i>
                    <span id="web-text4" class="web-text">Equipamentos</span>
                </span>
            </button>
            <button onclick="redirecionarRel()">
                <span>
                    <i class="material-symbols-outlined">quick_reference_all</i>
                    <span id="web-text5" class="web-text">Relatório</span>
                </span>
            </button>
            
        </nav>
        <button onclick="redirecionarSair()">
                <span>
                    <i class="material-symbols-outlined">logout</i>
                    <span id="web-text6" class="web-text">Sair</span>
                </span>
        </button>
        
    </aside>

    <!-- Menu mobile -->
    <div class="head-mobile">
        <img class="img" src="../../assets/logo.png" alt="logo_canastra">
        
        <button class="mobile-button" onclick="toggleMenu()">
            <i class="material-symbols-outlined">menu</i>
            
        
        </button>
        
    </div>

    <nav class="menu-mobile" id="menuMobile">
             <button class="button-close">
                <span>
                    <i class="material-symbols-outlined" onclick="toggleMenu()">close</i>
                   
                
                </span>
            </button>
            <button onclick="redirecionarInicio()">
                <span>
                    <i class="material-symbols-outlined">home</i>
                    <span class="mobile-text">Inicio</span>
                </span>
            </button>
            <button onclick="redirecionarAbrirOS()">
                <span>
                    <i class="material-symbols-outlined">file_open</i>
                    <span class="mobile-text">Ordem de Serviço</span>
                </span>
            </button>
            <button onclick="redirecionarPlanManu()">
                <span>
                    <i class="material-symbols-outlined">chronic</i>
                    <span class="mobile-text">Plano Manutenção</span>
                </span>
            </button>
            <button onclick="redirecionarEquip()">
                <span>
                    <i class="material-symbols-outlined">switch_access</i>
                    <span class="mobile-text">Equipamentos</span>
                </span>
            </button>
            <button onclick="redirecionarRel()">
                <span>
                    <i class="material-symbols-outlined">quick_reference_all</i>
                    <span class="mobile-text">Relatório</span>
                </span>
            </button>
            <button onclick="redirecionarSair()">
                <span>
                    <i class="material-symbols-outlined">logout</i>
                    <span class="mobile-text">Sair</span>
                </span>
        </button>
        </nav>
            
    

    <script src="../js/script_sidebar.js"></script>
    
</body>
</html>