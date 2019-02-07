
$(document).ready(function () {

    // date filter
    pickmeup('#analytics_datepicker_from', {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false,
    });

    pickmeup('#analytics_datepicker_to', {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false,
    });


    //select tag between clinic, custom and service
    $('.analytics-section').on('click', function () {
        $('.analytics-section').each(function () {
            if ($(this).hasClass('selected')){
                $(this).removeClass('selected');
                $($(this).data('section')).hide();
                $($(this).data('tab')).hide();
            }
        });
        $(this).addClass('selected');
        $($(this).data('section')).show();
        $($(this).data('tab')).show();
        $('.analytics-charts').show();
        $('.analytics-patient-list').hide();
        $('.analytics-patient-list-row').hide();
    });

    $('.oe-filter-options').each(function(){
        var id = $(this).data('filter-id');
        /*
        @param $wrap
        @param $btn
        @param $popup
      */
        enhancedPopupFixed(
            $('#oe-filter-options-'+id),
            $('#oe-filter-btn-'+id),
            $('#filter-options-popup-'+id)
        );

        // workout fixed poition

        var $allOptionGroups =  $('#filter-options-popup-'+id).find('.options-group');
        $allOptionGroups.each( function(){
            // listen to filter changes in the groups
            updateUI( $(this) );
        });

    });

    $('#js-chart-filter-global-anonymise').on('click', function () {
        if(this.checked){
            $('.drill_down_patient_list').hide();
        } else {
            $('.drill_down_patient_list').show();
        }
    });

    // update UI to show how Filter works
    // this is pretty basic but only to demo on IDG
    function updateUI( $optionGroup ){
        // get the ID of the IDG demo text element
        var textID = $optionGroup.data('filter-ui-id');
        var $allListElements = $('.btn-list li',$optionGroup);

        $allListElements.click( function(){
            $('#'+textID).text( $(this).text() );
            $allListElements.removeClass('selected');
            $(this).addClass('selected');
        });
    }

});
