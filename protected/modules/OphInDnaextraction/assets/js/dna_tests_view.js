$(function(){

    var showControls = function($element){

        $element.find(".frmDnaTests_controls").show();
        $element.find(".frmDnaTests_successmessage").hide();

        $element.find('.addTest').hide();
    };

    var hideControls = function($element){
        $element.find(".frmDnaTests_controls").hide();

        $element.find('.addTest').show();
    };

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
                is_remove_allowed: false
            },
            'success': function(html) {
                $transactions.append(html);
                $fieldset.find('.no-tests').hide();

                showControls( $fieldset );
            }
        });
    });

    $('form.frmDnaTests').on('click', '.removeTransaction', function(){
        var $form = $(this).closest('form');
        $(this).closest('tr').remove();

        if(!$form.find('tr.transaction-row').length) {
            $form.find('.no-tests').show();
        }

        showControls($form);

    });

    $(".cancelTest").click(function(e){
        var $form = $(this).closest('form');
        e.preventDefault();
        var alert = new OpenEyes.UI.Dialog.Confirm({
            content: 'Are you sure you want to cancel editing tests?',
            okButton: 'Yes, cancel',
            cancelButton: 'No, go back to editing'
        });
        alert.open();
        alert.on("ok", function(){
            $form.find('tbody.transactions tr.transaction-row:last-child').remove();
            hideControls($form);

            if(!$form.find('tr.transaction-row').length) {
                $form.find('.no-tests').show();
            }
        });
    });

    $(".submitTest").click(function(e){
        e.preventDefault();

        var $form = $(this).closest(".frmDnaTests"),
            $prev_section =  $form.closest('section').prev(),
            element_id = $(this).closest('section').data('element-id');

        $form.find(".msg").hide();

        var data = [], $tr = $form.find('tr:last-child');

        data.push(
           {name: "YII_CSRF_TOKEN", value: YII_CSRF_TOKEN},
           {name: "OphInDnaextraction_DnaTests_Transaction[0][element_id]", value: element_id},
           {name: "OphInDnaextraction_DnaTests_Transaction[0][date]", value: $tr.find('.dna-hasDatepicker').val()},
           {name: "OphInDnaextraction_DnaTests_Transaction[0][study_id]", value: $tr.find('select').val()},
           {name: "OphInDnaextraction_DnaTests_Transaction[0][volume]", value: $tr.find('.volume').val()},
           {name: "OphInDnaextraction_DnaTests_Transaction[0][comments]", value: $tr.find('td:nth-child(4) input').val()},
           {name: "Element_OphInDnaextraction_DnaExtraction[volume]", value: $prev_section.find('.volume').data('volume')}
        );

        $(".frmDnaTests_loader").show();

        hideControls($form);

        $.post($form.attr("action"), data,
            function(response){
                var response = JSON.parse(response);

                if(response.success)
                {
                    $form.find(".successmessage").show();
                    $(".frmDnaTests_loader").hide();

                    //set input to disabled
                    $("tr.transaction-row", $form).last().find('input').prop('disabled', true);

                }
                else
                {
                    var alert = new OpenEyes.UI.Dialog.Alert({
                        content: response.message
                    });
                    alert.open();
                    $(".frmDnaTests_loader").hide();
                    showControls($form);
                }
            }
        );
    });

    //invode datepicker on ajax inputs
    $('.transactions').on('click', '.dna-hasDatepicker', function(){
        $(this).datepicker({
            maxDate: 'today',
            dateFormat: 'd M yy'
        });
        $(this).datepicker("show");
    });

});
