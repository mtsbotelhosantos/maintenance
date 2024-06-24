// Função abrir menu mobile
function toggleMenu(){
    const menuMobile = document.getElementById("menuMobile")

    if(menuMobile.className === "menu-mobile"){
        menuMobile.className = "menu-mobile-active"
    }else{
        menuMobile.className = "menu-mobile"
    }
}

//Função mouseover no aside
function asideFunc() {
    const menuAside = document.getElementById("aside-web-id");
    const main = document.getElementById('main');
    const textAside = document.getElementById('web-text');
    const textAside2 = document.getElementById('web-text2');
    const textAside3 = document.getElementById('web-text3');
    const textAside4 = document.getElementById('web-text4');
    const textAside5 = document.getElementById('web-text5');
    const textAside6 = document.getElementById('web-text6');

    if (menuAside.classList.contains("aside-web")) {
        menuAside.classList.remove("aside-web");
        menuAside.classList.add("aside-web-close");


        [textAside, textAside2, textAside3, textAside4, textAside5, textAside6].forEach(elemento => {
            elemento.classList.remove("web-text");
            elemento.classList.add("web-text-close");
          });

      
    } else {
        menuAside.classList.remove("aside-web-close");
        menuAside.classList.add("aside-web");

        [textAside, textAside2, textAside3, textAside4, textAside5, textAside6].forEach(elemento => {
            elemento.classList.remove("web-text-close");
            elemento.classList.add("web-text");
          });

    }
}


//NAVEGAÇAO

function redirecionarAbrirOS() {
    window.location.href = 'ordens_corretivas.php';
}
function redirecionarAbrirOSEdit() {
    window.location.reload;
    return;
   
}
function redirecionarInicio() {
   window.location.href = 'inicio.php';
}
function redirecionarSair() {
     
    window.location.href = '../../logout.php';
}
function redirecionarEquip() {
     
    window.location.href = 'equipamentos.php';
}


function redirect_logo(){
    window.location.href = 'inicio.php';
}


function redirecionarPlanManu(){
    window.location.href = 'plano_manutencao_new.php';

}
function redirecionarRel(){
    window.location.href = 'relatorios.php';
}