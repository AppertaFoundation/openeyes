

<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
<?php
$this->getCustomVA(new DateTime());
?>

<div id="js-hs-chart-analytics-custom">

</div>

<script type="text/javascript">
    $(document).ready(function () {
        var data = [{
            name: 'custom section',
            x: [0,1,2,3,4,5],
            y: [12,13,14,16, 40,29],

        }];
        analytics_layout['title'] = "Custom Section";
        Plotly.newPlot(
            'js-hs-chart-analytics-custom', data ,analytics_layout
        );
    });
</script>
