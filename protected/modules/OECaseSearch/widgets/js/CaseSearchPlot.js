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
    });

    $('.results-options button').click(function() {
        let $selector = $(this).data('selector');
        if ($(this).text() === 'View as list') {
            $($selector).show();
            $(this).text('View as plot');
            $('.js-plotly-plot').each(function(index, item) {
                $(item).hide();
            });
        } else if ($(this).text() === 'View as plot') {
            $($selector).hide();
            $(this).text('View as list');
            let selected_var = $('#selected-variable').val();
            $('#' + selected_var).show();
        }
    });

    $('.js-plotly-plot').each(function(index, item) {
        let container = document.getElementById($(item).attr('id'));
        let data = [
            {
                x: $(item).data('x'),
                y: $(item).data('y'),
                type: 'bar',
                hovertemplate: $(item).data('var-name') + ': %{x}<br>(N: %{y})',
                name: ""
            }
        ];

        // layout
        const layout = oePlotly.getLayout({
            theme: ($('link[data-theme="light"]').prop('media') === 'none') ? 'dark' : 'light',
            plotTitle: $(item).data('var-label') + ' distribution N = ' + $(item).data('total'),
            legend: false,
            titleX: $(item).data('var-label') + $(item).data('var-unit'),
            titleY: false,
            numTicksX: 20,
            numTicksY: 20,
        });

        Plotly.newPlot(container, data, layout, {displayModeBar: false, responsive: true});
    });
});
