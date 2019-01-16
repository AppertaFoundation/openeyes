<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>

<div id="js-hs-chart-analytics-service">
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var service_layout = JSON.parse(JSON.stringify(analytics_layout));
        var service_data = <?= CJavaScript::encode($service_data); ?>;
        var overdue_data = [{
            name: "Overdue followups",
            x: Object.keys(service_data['overdue']),
            y: Object.values(service_data['overdue']).map(function (item, index) {
                return item.length;
            }),
            customdata: Object.values(service_data['overdue']),
            type: 'bar',
        }];
        var overdue_count = overdue_data[0]['y'].reduce((a, b) => a + b, 0);
        var coming_data =[{
            name: "Followups coming due",
            x: Object.keys(service_data['coming']),
            y: Object.values(service_data['coming']).map(function (item, index) {
                return item.length;
            }),
            customdata: Object.values(service_data['overdue']),
            type: 'bar',
        }];
        var coming_count = coming_data[0]['y'].reduce((a, b) => a + b, 0);
        service_layout['width'] = 700;

        Plotly.newPlot(
            'js-hs-chart-analytics-service', overdue_data ,service_layout, analytics_options
        );
    });
</script>