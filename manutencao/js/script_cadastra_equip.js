function confirmaOS(){
    
    var resultado = confirm("Você tem certeza que deseja cadastrar esse equipamento?");
if (resultado) {
    document.getElementById("abriros_form").submit();
    

} else {
    alert("Ok. Equipamento NÃO foi cadastrado!");
}

}