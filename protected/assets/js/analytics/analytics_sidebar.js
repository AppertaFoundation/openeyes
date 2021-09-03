const analytics_sidebar = (function () {
    // get throttle time
    const throttleTime = analytics_toolbox.getThrottleTime() || 100;
    const ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;

    return function () {
        const $clinical_plot_button = $('.clinical-plot-button');
        const $update_chart_btn = $('.update-chart-btn');
        // get current Specialty
        const specialty = analytics_toolbox.getCurrentSpecialty();
        const $tabs = $('.analytics-section');
        function selectSpecialtyOpt() {
            // for IF statement below
            // Service data options: service
            // Clinical data options: Clinical
            const selected_option = $(this).data('options');
            const $tab = $('.analytics-options-v2 button');

            // data section is used to save plot dom element id
            // Service data section: #js-hs-chart-analytics-service
            // Clinical data section: #js-hs-chart-analytics-clinical-main
            const selected_section = $(this).data('section');

            // data tab is used to save the menu dom element id under the tab
            // Service data tab: #js-charts-service
            // Clinical data tab: #js-charts-clinical
            const selected_tab = $(this).data('tab');

            // selected tab item
            const $selected_tab_item = $(`${selected_tab} li a.selected`);

            // display selected tab and the things related to it
            $(this).addClass('selected');
            $tabs.not(this).removeClass('selected');
            $(selected_section).show();

            $('#plot > div').not(selected_section).hide();
            $(selected_tab).show();
            $($tabs.not(this).data('tab')).hide();

            $('hr.divider').hide();
            
            $selected_tab_item.trigger('click')


            // force display when come back from other screen, like drill down
            // and hide drill down
            analytics_toolbox.hideDrillDownShowChart();

            // execute related function according to selected tab
            if (selected_option === 'clinical') {
                analytics_clinical();
            } else {
                analytics_service();
            }

            // drill down only cares plot, as the required data is with the plot
            analytics_drill_down(selected_section);
        }

        // options in clinical tab
        // All: Diagnoses
        // GL/MR: Diagnoses, Outcomes
        // ----------------------------------
        // Diagnoses: bring up Diagnoses plot
        // Outcomes: send ajax request for the plot
        function selectClinicalOpt(e) {
            // for Diagnoses
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('selected');
            $('.clinical-plot-button').not(this).removeClass('selected');
            $('.custom-filters > tbody > tr').hide();
            $('table.custom-filters tr.' + $(this).data('filterid') + '-filter').show();
            // force display when come back from other screen, like drill down
            // and hide drill down
            analytics_toolbox.hideDrillDownShowChart();

            $($(this).data('filterid')).show();
            $($(this).data('plotid')).show();
            $('#plot > div').not($($(this).data('plotid'))).hide();

            // Outcomes selection
            if ($(this).data('report').trim().toLowerCase() === 'outcomes') {
                $('#js-analytics-spinner').show();
                $.ajax({
                    url: '/analytics/getCustomPlot',
                    data: "YII_CSRF_TOKEN=" + YII_CSRF_TOKEN + '&' + $('#search-form').serialize() + '&' +
                        analytics_toolbox.getDataFilters() + '&specialty=' + specialty,
                    dataType: 'json',
                    success: function (data) {
                        // update custom data
                        analytics_dataCenter.custom.setCustomData(data);

                        // custom plot
                        analytics_custom();

                        // enable csv download for custom data
                        // the parameter indicate if the csv download is for
                        // custom data or not
                        analytics_csv_download();
                    },
                    complete: function () {
                        $('#js-analytics-spinner').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                    }
                });
            }
        }

        // update chart button
        function updateChart(e) {
            e.preventDefault();
            e.stopPropagation();

            // Service data options: service
            // Clinical data options: Clinical
            const selected_options = $('.analytics-section.selected').data('options');

            // get current plot for hiding and displaying
            const current_plot = $("#" + analytics_toolbox.getCurrentShownPlotId());

            current_plot.hide();

            $('#js-analytics-spinner').show();
            $.ajax({
                url: '/analytics/updateData',
                data: $('#search-form').serialize() + '&' +
                    analytics_toolbox.getDataFilters() +
                    '&report=' + $('#js-charts-service .charts li a.selected').data('report'),
                dataType: 'json',
                success: function (data) {
                    // TODO: only update current plot

                    // data structure
                    // data[0]: clinical data
                    // data[1]: service data
                    // data[2]: custom data
                    // data[va_final_ticks]: va_final_ticks

                    // for updating service and clinical data
                    analytics_dataCenter.clinical.setClinicalData(data[0]);
                    analytics_dataCenter.service.setServiceData(data[1]);
                    analytics_dataCenter.custom.setCustomData({
                        custom_data: data[2],
                        va_final_ticks: data['va_final_ticks']
                    });
                    current_plot.show();

                    // update plot and refresh csv download
                    if (selected_options === 'clinical') {
                        analytics_toolbox.plotUpdate(data, specialty, 'clinical');
                        analytics_csv_download();
                    } else {
                        analytics_toolbox.plotUpdate(data, specialty, 'service');
                        analytics_csv_download();
                    }
                },
                complete: function () {
                    $('#js-analytics-spinner').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                }
            });
        }

        function clearDate() {
            $('#analytics_datepicker_from').val('');
            $('#analytics_datepicker_to').val('');
        }

        $('#js-clear-date-range').off('click').on('click', _.throttle(clearDate, throttleTime));
        //Service, Clinical tab click
        $tabs.off('click').on('click', _.throttle(selectSpecialtyOpt, throttleTime));

        // from original code, don't where is it...
        $('#js-chart-filter-global-anonymise').off('click').on('click', function () {
            if (this.checked) {
                $('.drill_down_patient_list').hide();
            } else {
                $('.drill_down_patient_list').show();
            }
        });

        // from original code, don't where is it...
        const clinical_custom = $('#js-hs-clinical-custom')[0];

        if (clinical_custom) {
            $(clinical_custom).off('click').on('click', function () {
                $(this).addClass('selected');
                $clinical_plot_button.not(this).removeClass('selected');
            });
        }

        const clinical_vf = $('#js-hs-clinical-vf');

        if (clinical_vf) {
            $(clinical_vf).off('click').on('click', function () {
                $(this).addClass('selected');
                $clinical_plot_button.not(this).removeClass('selected');
            });
        }

        // bind click event on options in clinical tab
        // All: Diagnoses
        // GL/MR: Diagnoses, Outcomes
        $clinical_plot_button.off('click').on('click', _.throttle(selectClinicalOpt, ajaxThrottleTime));

        // bind submit event on search form wich is triggered bt Update Chart button
        $update_chart_btn.off('click').on('click', _.throttle(updateChart, ajaxThrottleTime));
    };
})();
