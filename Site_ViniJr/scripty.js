$(document).ready(function(){
    $("#scrollHome").click(function(){
        $("html, body").animate({
            scrollTop: 0
        }, 500);
    });

    $("#scrollNews").click(function(){
        $("html, body").animate({
            scrollTop: 686
        }, 500);
    });

    $("#scrollContact").click(function(){
        $("html, body").animate({
            scrollTop: 1420
        }, 500);
    });

    const form = document.getElementById('formulario');
    const messageBox = document.getElementById('messageBox');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        let isValid = true;

        const nome = document.getElementById('nome').value;
        if (nome.trim() === "") {
            alert("Por favor, insira seu nome.");
            isValid = false;
        }

        const email = document.getElementById('email').value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email.trim() === "" || !emailPattern.test(email)) {
            alert("Por favor, insira um email válido.");
            isValid = false;
        }

        const mensagem = document.getElementById('mensagem').value;
        if (mensagem.trim() === "") {
            alert("Por favor, insira sua mensagem.");
            isValid = false;
        }

        if (isValid) {
            messageBox.style.display = 'block';
            setTimeout(function() {
                messageBox.style.display = 'none';
            }, 3000);
            document.getElementById('nome').value = '';
            document.getElementById('email').value = '';
            document.getElementById('mensagem').value = '';
        }
    });
});

