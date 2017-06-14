$(document).ready(function () {

    $('#search_patient_disorder_id_0, #search_disorder_id_0, #genetics_patient_lookup').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});