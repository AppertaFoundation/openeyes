$(document).ready(function() {
    $('#selected-variable').change(function() {
        // Toggle visibility for all other plots
        $('.js-plotly-plot').each(function(index, item) {
            $(item).hide();
        });
        $('.js-plotly-plot[id="' + $(this).val() + '"]').show();
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
            titleX: $(item).data('var-label'),
            titleY: false,
            numTicksX: 20,
            numTicksY: 20,
        });

        Plotly.newPlot(container, data, layout, {displayModeBar: false, responsive: true});
    });
});
