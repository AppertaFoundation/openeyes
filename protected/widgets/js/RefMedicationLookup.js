$(function(){
    $(".js-ref-medication-lookup-autocomplete").autocomplete({
        minLength: 2,
        delay: 700,
        source: '/MedicationManagement/findRefMedications?ref_set_id=29',
        select: function(event, ui){
           $(event.target).siblings('input:hidden').val(ui.item.id);
        }
    });
});