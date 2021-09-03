const analytics_service = (function () {
    function switchFilter(){
        let type = $('#js-charts-service .charts li a.selected').data('report');
        $('.custom-filters > tbody > tr').hide();
        if(type === 'vf'){
            $('table.custom-filters tr.vf-filter').show()
        } else {
            $('table.custom-filters tr.service-filter').show()
        }
    }
    return function () {
        // get ajax url
        const url = analytics_dataCenter.ajax.getAjaxURL();
        // get service_data
        const service_data = analytics_dataCenter.service.getServiceData();
        // force csv update the data inside
        analytics_csv_download();

        // get related data according to report type
        const typeData = analytics_toolbox.getTypeData();

        // report_type_links: followups, overdue, waiting
        const report_type_links = $('#js-charts-service .charts li a');

        // put total number of each report onto the dom
        report_type_links.each(function () {
            const type = $(this).data('report');
            if (type !== 'vf') {
                $(this).text(typeData[type].htmlText + '(' + service_data['data_sum'][type] + ')');
            }
        });

        // defaultly load overdue report
        const overdue_raw = analytics_toolbox.processPlotData('overdue', service_data['plot_data']);
        analytics_toolbox.loadPlot('init', overdue_raw.data, overdue_raw.title);

        switchFilter();
        // bind click event
        report_type_links.off('click').on('click', function (e) {
            e.stopPropagation();
            $('#js-analytics-spinner').show();
            
            const $patient_list = $('.analytics-patient-list');

			// hide drill down patient list
			if ($patient_list.css('display') === 'block') {
                $patient_list.hide();
			}

            $('#js-hs-chart-analytics-service').hide();
            $('.js-hs-chart-analytics').hide();

            $(this).addClass('selected');
            $('#js-charts-service .charts li a').not(this).removeClass('selected');
            $('#js-hs-app-new').removeClass('selected');
            switchFilter();
            // get current clicked report type
            const type = $(this).data('report');
            const specialty = analytics_toolbox.getCurrentSpecialty();
            $.ajax({
                url: url,
                data: "YII_CSRF_TOKEN=" + YII_CSRF_TOKEN + '&' + $('#search-form').serialize() + '&' +
                analytics_toolbox.getDataFilters() + '&specialty=' + specialty + '&report=' + type,
                dataType: 'json',
                success: function (data) {
                    // update service data
                    analytics_dataCenter.service.setServiceData(data);

                    // redo the plotting
                    let plot_data;
                    if (type === 'vf') {
                        plot_data = data['plot_data'];
                        analytics_toolbox.loadPlot('click', plot_data, plot_data.title, type);
                        $('#js-btn-service').data(
                            'section',
                            '#js-hs-chart-analytics-vf, #js-hs-chart-analytics-vf-hedgehog, #js-hs-chart-analytics-vf-scatter'
                        );
                    } else {
                        plot_data = analytics_toolbox.processPlotData(type, data['plot_data']);
                        analytics_toolbox.loadPlot('click', plot_data.data, plot_data.title, type);
                        $('#js-btn-service').data('section', '#js-charts-service');
                    }

                    // force csv update the data inside
                    analytics_csv_download();

                    // bring back hidden plot
                    analytics_toolbox.hideDrillDownShowChart();
                    if (type === 'vf') {
                        $('#js-hs-chart-analytics-vf').show();
                        $('#js-hs-chart-analytics-vf-hedgehog').show();
                        $('#js-hs-chart-analytics-vf-scatter').show();
                        $('#js-hs-chart-analytics-service').hide();
                        $('hr.divider').show();
                    } else {
                        $('#js-hs-chart-analytics-service').show();
                        $('#js-hs-chart-analytics-vf').hide();
                        $('#js-hs-chart-analytics-vf-hedgehog').hide();
                        $('#js-hs-chart-analytics-vf-scatter').hide();
                        $('hr.divider').hide();
                    }

                },
                complete: function () {
                    $('#js-analytics-spinner').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                }
            });
        });
    };
})();
