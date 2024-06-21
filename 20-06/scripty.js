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
        scrollTop: 3300
    }, 200);
});

$("#scrollAbout").click(function(){
    $("html, body").animate({
        scrollTop: 4300
    }, 200);
});
});