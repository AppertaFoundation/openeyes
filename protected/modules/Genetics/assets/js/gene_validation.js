$(document).ready(function () {

    function validateGene($element) {

        var $input = $element;

        if( $input.val() !== ''){
            $input.closest('.row').find('.gene-validation-loader').show();
            $.getJSON('/Genetics/Gene/validateGene', {variant: $input.val()}, function(data){
                if(data.valid === true){
                    $input.removeClass('error-invalid error-failed');
                } else if (data.valid === false){
                    $input.addClass('error-invalid');
                } else {
                    $input.addClass('error-failed');
                }
            }).fail(function() {
                $input.addClass('error-failed');
            }).always(function(){
                $input.closest('.row').find('.gene-validation-loader').hide();
            });
        }
    }

    var $gene_inputs = $('.gene-validation');
    var gene_validation_timeout = null;

    $gene_inputs.on('change, keyup', function(){
        var $element = $(this);
        if(gene_validation_timeout){
            clearTimeout(gene_validation_timeout);
        }
        gene_validation_timeout = setTimeout(function(){
            var result = validateGene( $element );
        }, 1000);
    });

    $gene_inputs.closest('div').removeClass('end').after('<div class="large-3 column end hidden gene-validation-loader"><img src="'+baseUrl+OE_core_asset_path+'/img/ajax-loader.gif" class="loader" /> validating gene</div>');
    $gene_inputs.trigger('change');

});