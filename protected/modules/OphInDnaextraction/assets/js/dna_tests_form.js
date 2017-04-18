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

    $("#cancelTest").click(function(){
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
        var DNAExtraction_id = 1;
        $.post(
            "OphInDnaextraction/default/update-dna-withdrawals/"+DNAExtraction_id,
            {

            },
            function(data){

            }
        );
    });
});
