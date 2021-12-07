

//lead filter and reloade page
$(document).ready(function(){
    $('a#clear').click(function(e){
     e.preventDefault();
     let url = $('form#filter-form').attr('action');
     $('form#filter-form').find("input[type=text],input[type=email],input[type=password],input[type=number],input[type=date],textarea,select,checkbox,radio").val("");
     window.location.href = url;
    });




});
