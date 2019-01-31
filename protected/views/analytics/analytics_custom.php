
<div id="js-hs-chart-analytics-custom" style="display: none;">
    <div id="js-hs-chart-analytics-custom-right" style="display: block"></div>
    <div id="js-hs-chart-analytics-custom-left" style="display: none"></div>
</div>
<script type="text/javascript">
    var custom_layout, custom_data;
    $(document).ready(function () {
        custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        custom_data = <?= CJavaScript::encode($custom_data); ?>;
        custom_layout['xaxis']['rangeslider'] = {};
        custom_layout['yaxis2'] = {
            side: 'right',
            overlaying: 'y',
        };
        plot(true,custom_layout,custom_data[1]);
        plot(false,custom_layout,custom_data[0])
    });
    function plot(right,custom_layout, custom_data){
        var id;
        if (right){
            id = 'js-hs-chart-analytics-custom-right';
            custom_layout['title'] = "Custom Section (Right Eye)";
        } else {
            id = 'js-hs-chart-analytics-custom-left';
            custom_layout['title'] = "Custom Section (Left Eye)";
        }

        var custom_plot = document.getElementById(id);
        Plotly.newPlot(
            id, custom_data ,custom_layout, analytics_options
        );

        custom_plot.on('plotly_click', function (data) {
            for (var i = 0; i < data.points.length; i++) {
                $('.analytics-charts').hide();
                $('.analytics-patient-list').show();
                $('.analytics-patient-list-row').hide();
                var patient_show_list = data.points[i].customdata;
                for (var j = 0; j < patient_show_list.length; j++) {
                    $('#' + patient_show_list[j]).show();
                }
            }
        });
    }
</script>
