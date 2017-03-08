$(document).ready(function () {

    //pathetic trying to restrict this only form the add subject page now
    $('#GeneticsPatient_id').closest('form').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

});