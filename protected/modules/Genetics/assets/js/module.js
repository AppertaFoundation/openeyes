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

        if( $transcript.val() !== ''){
        $('.gene-validation-loader').show();
            $.getJSON('/' + OE_module_name + '/pedigree/validateGeneTranscript', {variant: $transcript.val()}, function(data){
                if(data.valid){
                    $transcript.removeClass('error');
                } else {
                    errorTranscript($transcript);
                }
            }).fail(function() {
                errorTranscript($transcript);
            }).always(function(){
                $('.gene-validation-loader').hide();
            });
        }
    }

    var $pedigree_gene_transcript = $('#Pedigree_gene_transcript');
    $pedigree_gene_transcript.on('change, keyup', validateGeneTranscript);
    $pedigree_gene_transcript.trigger('change');

    $pedigree_gene_transcript.closest('div').removeClass('end').after('<div class="large-3 column end hidden gene-validation-loader"><img src="'+baseUrl+OE_core_asset_path+'/img/ajax-loader.gif" class="loader" /> validating gene</div>');


});