

<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
<?php
?>

<div id="js-hs-chart-analytics-custom" style="display: none">

</div>

<script type="text/javascript">
    $(document).ready(function () {
        var custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        var custom_data = <?= CJavaScript::encode($custom_data); ?>;
        var custom_plot = document.getElementById('js-hs-chart-analytics-custom');
        custom_layout['title'] = "Custom Section";
        custom_layout['xaxis']['rangeslider'] = {};
        custom_layout['yaxis2'] = {
            side: 'right',
            overlaying: 'y',
        };
        Plotly.newPlot(
            'js-hs-chart-analytics-custom', custom_data ,custom_layout, analytics_options
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
    });
</script>
