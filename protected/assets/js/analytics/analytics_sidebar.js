
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

    //cataract plot change
    $('#js-chart-CA-selection').on('change', function () {
        $('#pcr-risk-grid').hide();
        $('#cataract-complication-grid').hide();
        $('#visual-acuity-grid').hide();
        $('#refractive-outcome-grid').hide();
        var selected_value = $(this).val();
        switch (selected_value) {
            case '0':
                $('#pcr-risk-grid').show();
                break;
            case '1':
                $('#cataract-complication-grid').show();
                break;
            case '2':
                $('#visual-acuity-grid').show();
                break;
            case '3':
                $('#refractive-outcome-grid').show();
                break;
        }
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

    $('update-chart-btn').on('click', function () {
        console.log($('input').val());
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
