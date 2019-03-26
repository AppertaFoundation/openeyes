$(document).ready(function(){
    $('.autosize').autosize();

    $('#et_delete_operator').click(function(e) {
        e.preventDefault();

        if ($('input[type="checkbox"][name="genericProcedures[]"]:checked').length == 0) {
            alert('Please select one or more generic procedure data to delete.');
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationnote/GenericProcedureData/delete',
            'data': $('input[type="checkbox"][name="genericProcedures[]"]:checked').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    window.location.reload();
                } else {
                    alert("Something went wrong trying to delete the generic operation data.\n\nPlease try again or contact support for assistance.");
                }
            }
        });
    });
});

