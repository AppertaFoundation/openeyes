const analytics_sidebar = (function () {
    // get throttle time
    const throttleTime = analytics_toolbox.getThrottleTime() || 100;
    const ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;

    return function () {
        // diagnosis filter in Service section, for update chart function
        const common_disorders_dom = $('.btn-list li');
        const common_disorders = common_disorders_dom.map(function (i, e) {
            return $(e).html();
        });

        const $clinical_plot_button = $('.clinical-plot-button');
        const $search_form = $('#search-form');

        // get user info
        const side_bar_user_list = analytics_dataCenter.user.getSidebarUser();
        const current_user = analytics_dataCenter.user.getCurrentUser();

        // get current Specialty
        const specialty = analytics_toolbox.getCurrentSpecialty();

        function selectSpecialtyOpt() {
            // data section is used to save plot dom element id
            // Service data section: #js-hs-chart-analytics-service
            // Clinical data section: #js-hs-chart-analytics-clinical-main
            const selected_section = $(this).data('section');

            // data tab is used to save the menu dom element id under the tab
            // Service data tab: #js-charts-service
            // Clinical data tab: #js-charts-clinical
            const selected_tab = $(this).data('tab');

            // for IF statement below
            // Service data options: service
            // Clinical data options: Clinical
            const selected_option = $(this).data('options');
            const $tab = $('.analytics-options-v2 button');

            // display selected tab and the things related to it
            $(this).addClass('selected');
            $tab.not(this).removeClass('selected');
            $(selected_section).show();
            $($tab.not(this).data('section')).hide();
            $('hr.divider').hide();
            $(selected_tab).show();
            $($tab.not(this).data('tab')).hide();

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
        // GL/MR: Diagnoses, Change in vision
        // ----------------------------------
        // Diagnoses: bring up Diagnoses plot
        // Change in vision: send ajax request for the plot
        function selectClinicalOpt(e) {
            // for Diagnoses
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('selected');
            $clinical_plot_button.not(this).removeClass('selected');
            $('.js-hs-chart-analytics-clinical').hide();
            $('.js-hs-filter-analytics-clinical').hide();

            // force display when come back from other screen, like drill down
            // and hide drill down
            analytics_toolbox.hideDrillDownShowChart();

            $($(this).data('filterid')).show();
            $($(this).data('plotid')).show();

            // Change in vision selection
            if ($(this).text().trim().toLowerCase() === 'change in vision') {
                $('#js-analytics-spinner').show();
                $.ajax({
                    url: '/analytics/getCustomPlot',
                    data: "YII_CSRF_TOKEN=" + YII_CSRF_TOKEN + '&' + $search_form.serialize() +
                        analytics_toolbox.getDataFilters(specialty, side_bar_user_list, common_disorders, current_user),
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
                data: $search_form.serialize() +
                    analytics_toolbox.getDataFilters(specialty, side_bar_user_list, common_disorders, current_user) +
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
        $('.analytics-section').off('click').on('click', _.throttle(selectSpecialtyOpt, throttleTime));


        // according to filter (green button, hover over will display filter options) selection
        // update filters text (below filter button)
        $('.oe-filter-options').each(function () {
            const id = $(this).data('filter-id');
            const $filter_options_popup_id = $('#filter-options-popup-' + id);
            /*
                @param $wrap
                @param $btn
                @param $popup
            */
            enhancedPopupFixed(
                $('#oe-filter-options-' + id),
                $('#oe-filter-btn-' + id),
                $filter_options_popup_id
            );

            // workout fixed poition

            const $allOptionGroups = $filter_options_popup_id.find('.options-group');
            $allOptionGroups.each(function () {
                // listen to filter changes in the groups
                analytics_toolbox.updateUI($(this));
            });

        });

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
        // GL/MR: Diagnoses, Change in vision
        $clinical_plot_button.off('click').on('click', _.throttle(selectClinicalOpt, ajaxThrottleTime));

        // bind submit event on search form wich is triggered bt Update Chart button
        $search_form.off('submit').on('submit', _.throttle(updateChart, ajaxThrottleTime));
    };
})();
