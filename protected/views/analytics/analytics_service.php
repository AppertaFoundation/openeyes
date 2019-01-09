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
        service_layout['margin']['l'] = 250;
        service_layout['yaxis']['showgrid'] = false;
        service_layout['yaxis']['tickvals'] = service_data['y'];
        service_layout['yaxis']['ticktext'] = service_data['text'];

        Plotly.newPlot(
            'js-hs-chart-analytics-service', data ,service_layout, analytics_options
        );
    });
</script>