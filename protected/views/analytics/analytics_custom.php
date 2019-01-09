

<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
<?php
?>

<div id="js-hs-chart-analytics-custom" style="display: none">

</div>

<script type="text/javascript">
    $(document).ready(function () {
        var custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        var custom_data = <?= CJavaScript::encode($custom_data); ?>;
        custom_layout['title'] = "Custom Section";
        custom_layout['xaxis']['rangeslider'] = {};
        custom_layout['yaxis2'] = {
            side: 'right',
            overlaying: 'y',
        };
        Plotly.newPlot(
            'js-hs-chart-analytics-custom', custom_data ,custom_layout, analytics_options
        );

        $('#js-hs-chart-analytics-custom').on('plotly_click', function (data) {
            $('.analytics-charts').hide();
            $('.analytics-patient-list').show();
        });
    });
</script>
