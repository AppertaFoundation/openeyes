
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

        $('#pcr-risk-grid').html("");
        $('#cataract-complication-grid').html("");
        $('#visual-acuity-grid').html("");
        $('#refractive-outcome-grid').html("");
        $('#analytics_datepicker_from').val("");
        $('#analytics_datepicker_to').val("");
        $('#analytics_allsurgeons').val("");
        $('.analytics-event-list-row').hide();
        $('.analytics-event-list').hide();
        $('#js-back-to-chart').hide();
        $('#js-all-surgeons').html('View all surgeons');
        var selected_value = $(this).val();
        switch (selected_value) {
            case '0':
                OpenEyes.Dash.init('#pcr-risk-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=PcrRisk&template=analytics', null, 10);
                break;
            case '1':
                OpenEyes.Dash.init('#cataract-complication-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=CataractComplications&template=analytics', null,10);
                break;
            case '2':
                OpenEyes.Dash.init('#visual-acuity-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\VisualOutcome&template=analytics', null, 10);
                break;
            case '3':
                OpenEyes.Dash.init('#refractive-outcome-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics', null, 10);
                break;
        }
        $('.mdl-cell').css('height','600px');
        $('.mdl-cell').css('width','1000px');

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
