<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>

<div id="js-hs-chart-analytics-service">
</div>

<script type="text/javascript">

    function constructPlotlyData(service_data) {
        var service_layout = JSON.parse(JSON.stringify(analytics_layout));
        service_layout['xaxis']['rangemode'] = 'nonnegative';

        var overdue_data = {
            name: "Overdue followups",
            x: Object.keys(service_data['overdue']),
            y: Object.values(service_data['overdue']).map(function (item, index) {
                return item.length;
            }),
            customdata: Object.values(service_data['overdue']),
            type: 'bar',
        };
        if (overdue_data['x'].length < 10) {
            overdue_data['width'] = 0.2;
        }

        var overdue_count = overdue_data['y'].reduce((a, b) => a + b, 0);
        var coming_data ={
            name: "Followups coming due",
            x: Object.keys(service_data['coming']),
            y: Object.values(service_data['coming']).map(function (item, index) {
                return item.length;
            }),
            customdata: Object.values(service_data['overdue']),
            type: 'bar',
        };
        if (coming_data['x'].length < 10 ) {
            coming_data['width'] = 0.2;
        }

        var coming_count = coming_data['y'].reduce((a, b) => a + b, 0);

        Plotly.newPlot(
            'js-hs-chart-analytics-service', [overdue_data] ,service_layout, analytics_options
        );

        $('#js-hs-app-follow-up-coming').html('Appointments: Follow Up(' + coming_count + ')');
        $('#js-hs-app-follow-up-overdue').html('Appointments: Delayed(' + overdue_count + ')');
        $('#js-hs-app-follow-up-coming').on('click', function () {
            $(this).addClass('selected');
            $('#js-hs-app-follow-up-overdue').removeClass('selected');
            $('#js-hs-app-new').removeClass('selected');
            $('#js-service-data-filter').show();

            Plotly.react(
                'js-hs-chart-analytics-service', [coming_data] ,service_layout, analytics_options
            );
        });

        $('#js-hs-app-follow-up-overdue').on('click', function () {
            $(this).addClass('selected');
            $('#js-hs-app-follow-up-coming').removeClass('selected');
            $('#js-hs-app-new').removeClass('selected');
            $('#js-service-data-filter').hide();

            Plotly.react(
                'js-hs-chart-analytics-service', [overdue_data] ,service_layout, analytics_options
            );
        });

        var service_plot = document.getElementById('js-hs-chart-analytics-service');
        service_plot.on('plotly_click', function (data) {
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

    $(document).ready(function () {
        var service_data = <?= CJavaScript::encode($service_data); ?>;
        constructPlotlyData(service_data);
    });
</script>