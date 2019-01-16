

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
        var chart_left = $('#js-hs-chart-analytics-custom-left')[0];
        var chart_right = $('#js-hs-chart-analytics-custom-right')[0];
        chart_left.data[0]['x'] = data[0][0]['x'];
        chart_left.data[0]['y'] = data[0][0]['y'];
        chart_left.data[0]['customdata'] = data[0][0]['customdata'];
        chart_left.data[1]['x'] = data[0][1]['x'];
        chart_left.data[1]['y'] = data[0][1]['y'];
        chart_left.data[1]['customdata'] = data[0][1]['customdata'];
        Plotly.redraw(chart_left);
        chart_right.data[0]['x'] = data[1][0]['x'];
        chart_right.data[0]['y'] = data[1][0]['y'];
        chart_right.data[0]['customdata'] = data[1][0]['customdata'];
        chart_right.data[1]['x'] = data[1][1]['x'];
        chart_right.data[1]['y'] = data[1][1]['y'];
        chart_right.data[1]['customdata'] = data[1][1]['customdata'];
        Plotly.redraw(chart_right);
    }
</script>
