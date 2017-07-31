function toggleNoPedigreeCheckbox(){

    var $ul = $('#div_GeneticsPatient_Pedigree').find('ul.MultiSelectList'),
        $no_pedigree = $('#no_pedigree');

    if( $ul.find('li').length < 1 ){
        $no_pedigree.closest('.row').show();
    } else {
        $no_pedigree.closest('.row').hide();
    }
}

$(document).ready(function () {

    var $pedigree_row = $('#div_GeneticsPatient_Pedigree');

    $('#search_patient_disorder_id_0, #search_disorder_id_0, #genetics_patient_lookup').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $pedigree_row.on('click', '.remove-one', function(){

        //as no callback in MultiSelectList.js
        setTimeout(toggleNoPedigreeCheckbox, 0.5);
    });
});