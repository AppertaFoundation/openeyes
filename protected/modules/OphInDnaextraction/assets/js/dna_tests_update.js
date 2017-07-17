$(document).ready(function() {

    //invode datepicker on ajax inputs
    $('.transactions').on('click', '.dna-hasDatepicker', function(){
        $(this).datepicker({
            maxDate: 'today',
            dateFormat: 'd M yy'
        });
        $(this).datepicker("show");
    });

    $('fieldset.dnatests').on('click', '.removeTransaction', function(){
        var $form = $(this).closest('form');
        $(this).closest('tr').remove();

        if(!$form.find('tr.transaction-row').length) {
            $form.find('.no-tests').show();
        }
    });

    $('.addTest').click(function(e) {
        e.preventDefault();
        var index,
            $fieldset = $(this).closest('fieldset'),
            $transactions = $fieldset.find('tbody.transactions');

        index = $("tr.transaction-row", $fieldset).last().data('index');

        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/OphInDnaextraction/default/addTransaction',
            data:{
                i: (index === undefined ? 0 : index + 1),
                is_remove_allowed: true
            },
            'success': function(html) {
                $transactions.append(html);
                $fieldset.find('.no-tests').hide();
            }
        });
    });
});