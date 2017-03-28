$(document).ready(function () {

    //pathetic trying to restrict this only form the add subject page now
    $('#GeneticsPatient_id').closest('form').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    function errorTranscript($transcript) {
        $transcript.addClass('error');
    }

    function validateGeneTranscript() {
        var $transcript = $(this);
        $.getJSON('/' + OE_module_name + '/pedigree/validateGeneTranscript', {variant: $transcript.val()}, function(data){
            if(data.valid){
                $transcript.removeClass('error');
            } else {
                errorTranscript($transcript);
            }
        }).fail(function() {
            errorTranscript($transcript);
        });
    }

    var $pedigree_gene_transcript = $('#Pedigree_gene_transcript');
    $pedigree_gene_transcript.on('change, keyup', validateGeneTranscript);
    $pedigree_gene_transcript.trigger('change');

});