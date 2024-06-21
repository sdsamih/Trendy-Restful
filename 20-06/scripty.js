$(document).ready(function(){
$("#scrollHome").click(function(){
    $("html, body").animate({
        scrollTop: 0
    }, 500);
});

const form = document.getElementById('formulario');

    // Captura a caixa de mensagem
    const messageBox = document.getElementById('messageBox');

    // Adiciona um ouvinte de evento para o envio do formulário
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Previne o envio padrão do formulário

        // Exibe a caixa de mensagem
        messageBox.style.display = 'block';
    });


    
$("#scrollNews").click(function(){
    $("html, body").animate({
        scrollTop: 686
    }, 500);
});

$("#scrollContact").click(function(){
    $("html, body").animate({
        scrollTop: 3300
    }, 200);
});

$("#scrollAbout").click(function(){
    $("html, body").animate({
        scrollTop: 4300
    }, 200);
});
});