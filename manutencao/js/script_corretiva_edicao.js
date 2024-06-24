// funcoes para esconder e mostrar as abas de apontamento e fechamento de OS

function abreFormClose() {
    var apto = document.getElementById('apontamentos-paradas');
    var close = document.getElementById('form-os-close');
    

  close.style.display = 'flex';
  apto.style.display = 'none';  
  
}

function abreFormApto(){
    var apto = document.getElementById('apontamentos-paradas');
    var close = document.getElementById('form-os-close');

  
        apto.style.display = 'flex';
        close.style.display = 'none';
    
  
}

//#########funcoes botoes de apontamento

//abrir div iniciar apontamento
function abrirDivApto(){
  var divIniciaApt = document.getElementById('iniciar_apt_button_div');
  var button = document.getElementById('button_inicia_ap');

    divIniciaApt.style.display = 'flex';
    button.style.display = 'none';


}
//cancela abertura
function cancelaAbertura(){
  var divIniciaApt = document.getElementById('iniciar_apt_button_div');
  var button = document.getElementById('button_inicia_ap');

    divIniciaApt.style.display = 'none';
    button.style.display = 'flex';
}


//iniciar parada
function iniciarParada(){
  var button = document.getElementById('inicia_parada_button');
  var divIniciaParada = document.getElementById('div_inicia_parada');

    button.style.display = 'none';
    divIniciaParada.style.display = 'flex';

}
function cancelaParada(){
  var button = document.getElementById('inicia_parada_button');
  var divIniciaParada = document.getElementById('div_inicia_parada');

    button.style.display = 'flex';
    divIniciaParada.style.display = 'none';

}

//finalizar apontamento
function finalizarApt(){
  var button = document.getElementById('finaliza_apt_button');
  var text_input = document.getElementById('text_finaliza_apt');
  var submitt_input = document.getElementById('submit_finaliza_apt');

    button.style.display = 'none';
    text_input.style.display = 'flex';
    submitt_input.style.display = 'flex';

}


//Funcao Mostra Edição de Data Nova para fechamento prevista


function NewDatePrev() {
  var form = document.getElementById('altera_data_prev');
 
  if(form.style.display == 'none'){
    form.style.display = 'flex';
  }else{
    form.style.display = 'none';
  }


}

