

<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
<?php
?>

<div id="js-hs-chart-analytics-custom-right" style="display: none">

</div>
<div id="js-hs-chart-analytics-custom-left" style="display: none">

</div>
<script type="text/javascript">
    var custom_layout, custom_data;
    $(document).ready(function () {
        custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        custom_data = <?= CJavaScript::encode($custom_data); ?>;
        custom_layout['title'] = "Custom Section";
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
            id = 'js-hs-chart-analytics-custom-right'
        } else {
            id = 'js-hs-chart-analytics-custom-left'
        }
        var custom_plot = document.getElementById(id);
        Plotly.newPlot(
            id, custom_data ,custom_layout, analytics_options
        );
        custom_plot.on('plotly_click', function (data) {
            for(var i=0; i < data.points.length; i++){
                $('.analytics-charts').hide();
                $('.analytics-patient-list').show();
                $('.analytics-patient-list-row').hide();
                var patient_show_list = data.points[i].customdata;
                for (var j=0; j< patient_show_list.length; j++){
                    $('#'+patient_show_list[j]).show();
                }
            }
        });
    }
    function plotUpdate(data){
        var charts = ['js-hs-chart-analytics-custom-left','js-hs-chart-analytics-custom-right'];
        for (var i = 0; i < charts.length; i++) {
            var chart = $('#'+charts[i])[0];
            chart.data[0]['x'] = data[i][0]['x'];
            chart.data[0]['y'] = data[i][0]['y'];
            chart.data[0]['customdata'] = data[0][0]['customdata'];
            chart.data[1]['x'] = data[i][1]['x'];
            chart.data[1]['y'] = data[i][1]['y'];
            chart.data[1]['customdata'] = data[0][1]['customdata'];
            Plotly.redraw(chart);
        }
    }
</script>
