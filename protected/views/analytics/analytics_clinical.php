<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>

<div id="js-hs-chart-analytics-clinical" style="display: none">
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var clinical_layout = JSON.parse(JSON.stringify(analytics_layout));
        var clinical_data = <?= CJavaScript::encode($clinical_data); ?>;
        var data = [{
            name: clinical_data['title'],
            x: clinical_data['x'],
            y: clinical_data['y'],
            customdata: clinical_data['customdata'],
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
        var clinical_plot = document.getElementById('js-hs-chart-analytics-clinical');

        clinical_plot.on('plotly_click', function(data){
            var custom_data = data.points[0].customdata;
            if ('text' in custom_data && 'customdata' in custom_data){
                custom_data['name'] = 'Other Disorders';
                custom_data['type'] = 'bar';
                custom_data['orientation'] = 'h';
                clinical_layout['yaxis']['tickvals'] = custom_data['y'];
                clinical_layout['yaxis']['ticktext'] = custom_data['text'];
                Plotly.newPlot(
                    'js-hs-chart-analytics-clinical', [custom_data], clinical_layout, analytics_options
                );
            }
            else{
                //redirect to drill down patient list
            }
        });
    });
</script>