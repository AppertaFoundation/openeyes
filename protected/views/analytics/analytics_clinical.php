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
        clinical_layout['margin']['l'] = 250;
        clinical_layout['yaxis']['showgrid'] = false;
        clinical_layout['yaxis']['tickvals'] = clinical_data['y'];
        clinical_layout['yaxis']['ticktext'] = clinical_data['text'];
        Plotly.newPlot(
            'js-hs-chart-analytics-clinical', data ,clinical_layout, analytics_options
        );
    });
</script>