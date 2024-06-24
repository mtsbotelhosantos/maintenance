function confirmaCria(){
    
    var resultado_cria = confirm("Você tem certeza que deseja criar esse plano de manutenção?");
if (resultado_cria) {
    document.getElementById("abriros_form").submit();
    

} else {
    alert("Ok. Plano NÃO criado!");
}

}

function confirmaOS(){
    
    var resultado = confirm("Você tem certeza que deseja abrir essa ordem de serviço?");
if (resultado) {
    document.getElementById("abriros_form").submit();
    

} else {
    alert("Ok. Ordem de serviço NÃO foi aberta!");
}

}

function confirmaAlteraOS(){
    
    var resultado = confirm("Você tem certeza que deseja ALTERAR a data desse plano de manutenção?");
if (resultado) {
    document.getElementById("abriros_form").submit();
    

} else {
    alert("Ok. Data NÃO foi alterada!");
}

}

