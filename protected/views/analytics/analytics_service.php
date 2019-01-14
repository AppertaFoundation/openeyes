<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>

<div id="js-hs-chart-analytics-service">
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var service_layout = JSON.parse(JSON.stringify(analytics_layout));
        var service_data = <?= CJavaScript::encode($service_data); ?>;
        var data = [{
            name: service_data['title'],
            x: service_data['x'],
            y: service_data['y'],
            type: 'bar',
            orientation: 'h'
        }];
        service_layout['width'] = 700;

        Plotly.newPlot(
            'js-hs-chart-analytics-service', data ,service_layout, analytics_options
        );
    });
</script>