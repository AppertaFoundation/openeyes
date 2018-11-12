$(function(){
    $(".js-ref-medication-lookup-autocomplete").autocomplete({
        minLength: 2,
        delay: 700,
        source: '/MedicationManagement/findRefMedications',
        select: function(event, ui){
           $(event.target).siblings('input:hidden').val(ui.item.id);
        }
    });
});