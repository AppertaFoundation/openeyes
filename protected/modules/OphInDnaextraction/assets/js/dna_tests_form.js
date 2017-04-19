$(function(){
    $('.addTest').click(function(e) {
        e.preventDefault();

        var i = 0;

        $('tbody.transactions').children('tr').children('td').children('input:first').map(function() {
            var id = $(this).attr('name').match(/[0-9]+/);

            if (id >= i) {
                i = id;
            }
        });

        $.ajax({
            'type': 'GET',
            'url': baseUrl+'/OphInDnaextraction/default/addTransaction?i='+i,
            'success': function(html) {
                $('tbody.transactions').append(html);
                $('#no-tests').hide();
            }
        });
    });

    $('.removeTransaction').die('click').live('click',function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
        if(!$('.removeTransaction').length) {
            $('#no-tests').show();
        }
    });

    $("#cancelTest").click(function(e){
        e.preventDefault();
        var alert = new OpenEyes.UI.Dialog.Confirm({
            content: 'Are you sure you want to cancel editing tests?',
            okButton: 'Yes, cancel',
            cancelButton: 'No, go back to editing'
        });
        alert.open();
        alert.on("ok", function(){
            window.location.reload();
        });
    });

    $(".submitTest").click(function(e){
        e.preventDefault();
        var $form = $("#frmDnaTests");
        $form.find(".msg").hide();
        var data = $form.serializeArray();
        $.post($form.attr("action"), data,
            function(response){
                if(response.success)
                {
                    $form.find(".successmessage").show();
                }
                else
                {
                    var alert = new OpenEyes.UI.Dialog.Alert({
                        content: response.message
                    });
                    alert.open();
                }
            }
        );
    });
});
