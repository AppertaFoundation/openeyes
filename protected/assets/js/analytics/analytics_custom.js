const analytics_custom = (function () {
    return function () {
        // get plot layout settings
        let custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        // get custome data
        const data = analytics_dataCenter.custom.getCustomData();
        const custom_data = data['custom_data'];
        const va = data['va_final_ticks'];

        const specialty = analytics_toolbox.getCurrentSpecialty();
        $('.custom-filters > tbody > tr').hide();
        $('table.custom-filters tr.custom-filter').show()

        function plot(side, custom_layout, custom_data) {
            const id = 'js-hs-chart-analytics-clinical-others-' + side;
            const custom_plot = document.getElementById(id);
            $('#' + id).css('display', 'block');
            custom_layout['title']['text'] = document.querySelector('td[data-name="analytics_procedure"] span').dataset.label + ": " + side;
            custom_layout['yaxis2'] = analytics_toolbox.setYaxisRange(custom_data[1], custom_layout['yaxis2']);
            Plotly.newPlot(
                id, custom_data, custom_layout, analytics_options
            );

            analytics_drill_down(custom_plot, custom_data);
        }
        custom_layout = analytics_toolbox.setCustomLayout(specialty, va);
        plot('right', custom_layout, custom_data[1]);
        plot('left', custom_layout, custom_data[0]);
        $('#js-hs-chart-analytics-clinical-others-left').hide();
        $('.custom-filter input[name$="outcome-eye"]').off('change').on("change", function (e) {
            const $checked_eye_filter = $('.custom-filter input[name$="outcome-eye"]:checked');
            if($checked_eye_filter.length === 0){
                $(this).prop('checked', true);
                return;
            }
            const side = $(this).data('side');
            const current_plot_id = '#js-hs-chart-analytics-clinical-others-' + side;

            if ($(this).attr('checked')) {
                custom_layout['title']['text'] = document.querySelector('td[data-name="analytics_procedure"] span').dataset.label + ": " + side;
                $(current_plot_id).css('display', 'block');
            } else {
                $(current_plot_id).css('display', 'none');
            }

        });
        $('.custom-filter input[name$="outcome-eye"]').trigger('change');
    }
})();
