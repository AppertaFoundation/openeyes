const analytics_init = (function () {
    const ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;
    return function () {
        const $oescape_icon_btns = $('.oe-full-header .oescape-icon-btns a');
        function selectSpecialty(e) {
            e.preventDefault();

            // display spinner
            $('#js-analytics-spinner').show();

            analytics_toolbox.hideDrillDownShowChart();

            $(this).removeClass('inactive');

            $(this).addClass('active selected');

            $oescape_icon_btns.not(this).removeClass('inactive selected');
            $oescape_icon_btns.not(this).addClass('inactive');

            const target = $(this).data('link');

            analytics_dataCenter.ajax.setAjaxURL(target);

            const specialty = analytics_toolbox.getCurrentSpecialty();

            $('#specialty').text(specialty);

            $.ajax({
                url: target,
                type: "POST",
                data: {
                    "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                    "specialty": specialty,
                },
                success: function (response) {
                    const data = response;
                    let $plot = $('#plot');
                    analytics_dataCenter.specialtyData.setResponseData(data);
                    $('#sidebar').html(data['dom']['sidebar']);
                    $plot.html(data['dom']['plot']);
                    $plot.html(data['dom']['drill']);
                    if (specialty.toLowerCase() === 'cataract') {
                        $('#js-analytics-spinner').hide();
                        // clear search criteria when navigate to cataract screen
                        analytics_dataCenter.cataract.clearCataractSearchForm();
                        analytics_cataract(data['data']);
                        return;
                    }
                    // load Sidebar
                    analytics_sidebar();
                    // defaultly load Service screen
                    analytics_service();
                    // initialize datePicker
                    analytics_toolbox.initDatePicker();
                    // load drill down
                    analytics_drill_down(null);
                },
                complete: function () {
                    $('#js-analytics-spinner').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                }
            });
        }

        // specialty options buttons: All CA GL MR
        $oescape_icon_btns.on('click', _.throttle(selectSpecialty, ajaxThrottleTime));

        $('#js-all-specialty-tab').click();
    };
})();
