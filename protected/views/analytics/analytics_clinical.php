<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>

<div id="js-hs-chart-analytics-clinical">
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var data = [{
            name: 'clinical section',
            x: [0,1,2,3,4,5],
            y: [12,13,14,16, 40,29],

        }];
        analytics_layout['title'] = "Clinical Section";
        Plotly.newPlot(
            'js-hs-chart-analytics-clinical', data ,analytics_layout
        );
    });
</script>
