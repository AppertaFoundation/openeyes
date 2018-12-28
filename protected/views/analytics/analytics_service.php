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
        service_layout['title'] = "Service Section";
        service_layout['yaxis']['tickvals'] = [0, 1, 2, 3, 4, 5];
        service_layout['yaxis']['ticktext'] = ['a','b','C','D', 'E', 'F'];

        Plotly.newPlot(
            'js-hs-chart-analytics-service', data ,service_layout
        );
    });
</script>