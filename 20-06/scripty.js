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
        messageBox.style.display = 'block';
        setTimeout(function() {
            messageBox.style.display = 'none';
        }, 3000);
    });

});

