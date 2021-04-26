$(document).ready(function() {
    $('#selected-variable').change(function() {
        // Toggle visibility for all other plots
        if ($('.results-options button').text() === 'View as list') {
            $('.js-plotly-plot').each(function(index, item) {
                $(item).hide();
            });
            // Only display the plot if the user is not currently viewing the results as a list.
            $('.js-plotly-plot[id="' + $(this).val() + '"]').show();
        }
        $('input[name="var"]').val($(this).val());
    });

    $('.results-options button').click(function() {
        let $selector = $(this).data('selector');
        if ($(this).text() === 'View as list') {
            $($selector).show();
            $(this).text('View as plot');
            $('.js-plotly-plot').each(function(index, item) {
                $(item).hide();
            });
            $('.oe-search-drill-down-list').hide();
        } else if ($(this).text() === 'View as plot') {
            $($selector).hide();
            $(this).text('View as list');
            let selected_var = $('#selected-variable').val();
            $('#' + selected_var).show();
            $('.oe-search-results').hide();
            $('.oe-search-drill-down-list').hide();
        }
    });

    $('.js-plotly-plot').each(function(index, item) {
        let container = document.getElementById($(item).attr('id'));
        let theme = ($('link[data-theme="light"]').prop('media') === 'none') ? 'dark' : 'light';

        // layout
        let layout = oePlotly_v1.getLayout({
            theme: theme,
            plotTitle: $(item).data('var-label') + ' distribution N = ' + $(item).data('total'),
            legend: false,
            titleX: $(item).data('var-unit'),
            titleY: false,

        });

        // Override the tick options (as we don't necessarily want to use the default tick options in oePlotly,
        // but still want the other layout options currently present in oePlotly.getLayout().
        layout.xaxis.tick0 = parseFloat($(item).data('min-value'));
        layout.xaxis.dtick = parseFloat($(item).data('bin-size'));
        layout.xaxis.nticks = null;
        layout.xaxis.tickmode = 'linear';

        let data = [
            {
                x: $(item).data('x'),
                y: $(item).data('y'),
                type: 'bar',
                hovertemplate: $(item).data('var-name') + ': %{x}<br>(N: %{y})',
                name: "",
                customdata: $(item).data('patient-id-list'),
            },
        ];

        Plotly.newPlot(container, data, layout, {displayModeBar: false, responsive: true});

        // When a data point is clicked on, display the list of applicable patients (drill-down).
        container.on('plotly_click', function(data) {
            $('#js-analytics-spinner').show();
            $.ajax({
                url: '/OECaseSearch/caseSearch/getDrilldownList?patient_ids=' + data.points[0].customdata[0],
                success: function(response) {
                    // Insert the drill-down list contents into the DOM.
                    $('.oe-search-drill-down-list').remove();
                    $('main.oe-full-main').append(response);
                    $('.results-options button').text('View as plot');
                    $('.js-plotly-plot').each(function(index, item) {
                        $(item).hide();
                    });
                    $('#js-analytics-spinner').hide();
                },
                error: function() {
                    $('#js-analytics-spinner').hide();
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Unable to retrieve drill-down list for selected data point.'
                    }).open();
                }
            });
        });
    });
});
