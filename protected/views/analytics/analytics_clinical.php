<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>

<div id="js-hs-chart-analytics-clinical" style="display: none">
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var clinical_plot = $('#js-hs-chart-analytics-clinical');
        var clinical_layout = JSON.parse(JSON.stringify(analytics_layout));
        var clinical_data = <?= CJavaScript::encode($clinical_data); ?>;
        var data = [{
            name: clinical_data['title'],
            x: clinical_data['x'],
            y: clinical_data['y'],
            type: 'bar',
            orientation: 'h'

        }];
        clinical_layout['title'] = "Clinical Section";
        clinical_layout['yaxis']['tickvals'] = [0, 1, 2, 3, 4, 5];
        clinical_layout['yaxis']['ticktext'] = ['a','b','C','D', 'E', 'F'];
        Plotly.newPlot(
            'js-hs-chart-analytics-clinical', data ,clinical_layout
        );

        clinical_plot.on('plotly_click', function () {
        });
    });
</script>