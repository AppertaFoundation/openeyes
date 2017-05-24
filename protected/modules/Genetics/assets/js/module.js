$(document).ready(function () {

    //pathetic trying to restrict the form being sent on Enter
    $('#GeneticsPatient_id').closest('form').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});