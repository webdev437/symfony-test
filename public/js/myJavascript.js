$(document).ready(function(){
    $(".message-box").fadeTo(4000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
    $("#remove_subuser").click(function(){
        if (confirm("will you remove seleted users really?")){
           $('form#selected_subuser_del').submit();
         }
    });
    
});



