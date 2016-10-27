$(document).ready(function(){
    
    $('#patient-form').on('keyup', '#Patient_nhs_num', function(){
        var selector = $(this).data('child_row');
        $(selector).hide();
        if( $(this).val().length > 0 ){
            $(selector).show();
        }
    });
    
    $('#Patient_is_deceased').on('change', function(){
        var selector = $(this).data('child_row');
        $(selector).hide();
        $(selector).find('input').val('');
        if($(this).is(':checked')){
            $(selector).show();
        }
    });
    
    
});