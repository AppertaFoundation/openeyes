const analytics_custom = (function () {
    return function () {
        // get plot layout settings
        let custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        // get custome data
        const data = analytics_dataCenter.custom.getCustomData();
        const custom_data = data['custom_data'];
        const va = data['va_final_ticks'];

        const specialty = analytics_toolbox.getCurrentSpecialty();

        function plot(right, custom_layout, custom_data) {
            let id;
            const op_type = specialty === 'Glaucoma' ? 'procedure' : 'treatment';
            if (right) {
                id = 'js-hs-chart-analytics-clinical-others-right';
                custom_layout['title']['text'] = $('#js-chart-filter-' + op_type).text() + ": Right";
            } else {
                id = 'js-hs-chart-analytics-clinical-others-left';
                custom_layout['title']['text'] = $('#js-chart-filter-' + op_type).text() + ": Left";
            }
            custom_layout['yaxis2'] = analytics_toolbox.setYaxisRange(custom_data[1], custom_layout['yaxis2']);

            const custom_plot = document.getElementById(id);
            Plotly.newPlot(
                id, custom_data, custom_layout, analytics_options
            );

            analytics_drill_down(custom_plot, custom_data);
        }

        custom_layout['title'] = {
            text: '',
            font: {
                color: '#fff'
            },
        };

        custom_layout = analytics_toolbox.setXaxisTick(custom_layout);

        custom_layout['xaxis']['rangeslider'] = {};
        custom_layout['yaxis']['title'] = analytics_toolbox.getVATitle();
        custom_layout['yaxis']['side'] = 'right';

        //Set VA unit tick labels
        const va_mode = $('#js-chart-filter-plot');
        if (va_mode.html().includes('absolute')) {
            custom_layout['yaxis']['tickmode'] = 'array';
            custom_layout['yaxis']['tickvals'] = va['tick_position'];
            custom_layout['yaxis']['ticktext'] = va['tick_labels'];
        } else {
            custom_layout['yaxis']['tickmode'] = 'auto';
        }

        custom_layout['yaxis2'] = {
            title: specialty === 'Glaucoma' ? "IOP (mm Hg)" : "CRT &mu;m",
            titlefont: {
                family: 'sans-serif',
                size: 12,
                color: '#fff',
            },
            side: 'left',
            overlaying: 'y',
            linecolor: '#fff',
            tickcolor: '#fff',
            tickfont: {
                color: '#fff',
            },
        };
        custom_layout['legend'] = {
            x: 0,
            y: 1
        };
        plot(true, custom_layout, custom_data[1]);
        plot(false, custom_layout, custom_data[0]);
        $('hr.divider').hide();
        $('#js-btn-selected-eye').click(function () {
            $('#js-chart-filter-eye-side').trigger("changeEyeSide");
        });
        $('#js-chart-filter-eye-side').bind("changeEyeSide", function () {
            const side = $('#js-chart-filter-eye-side').text().toLowerCase();
            const opposite_side = side === 'left' ? 'right' : 'left';
            $('#js-hs-chart-analytics-clinical-others-' + side).show();
            $('#js-hs-chart-analytics-clinical-others-' + opposite_side).hide();
        });

        $('#js-chart-filter-age').on('DOMSubtreeModified', function () {
            if ($('#js-chart-filter-age').html() === "Range") {
                $('#js-chart-filter-age-all').hide();
                $('#js-chart-filter-age-min').addClass('js-hs-filters');
                $('#js-chart-filter-age-max').addClass('js-hs-filters');
                $('#js-chart-filter-age-range').show();
            } else {
                $('#js-chart-filter-age-range').hide();
                $('#js-chart-filter-age-min').removeClass('js-hs-filters');
                $('#js-chart-filter-age-max').removeClass('js-hs-filters');
                $('#js-chart-filter-age-all').show();
            }
        });


    };
})();
