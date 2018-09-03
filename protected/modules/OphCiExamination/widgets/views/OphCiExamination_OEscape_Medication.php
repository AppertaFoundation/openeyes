<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/highchart-Meds.js')?>"></script>

<div id="js-hs-chart-Meds" class="highchart-area" data-highcharts-chart="2" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
    <div id="highcharts-Meds-right" class="highcharts-Meds highcharts-right highchart-section"></div>
    <div id="highcharts-Meds-left" class="highcharts-Meds highcharts-left highchart-section" style="display: none;"></div>
</div>


<script type="text/javascript">
  $(document).ready(function () {
    var meds_data = <?= CJavaScript::encode($this->getMedicationList()); ?>;
    var sides = ['left', 'right'];
    var chart_Med = {};
    var series_no = Math.max(Object.keys(meds_data['left']).length, Object.keys(meds_data['right']).length);
    for (var i in sides) {
      setSeriesNo(series_no);
      optionsMeds['xAxis']['categories'] = Object.keys(meds_data[sides[i]]);
      chart_Med[sides[i]] = new Highcharts.chart('highcharts-Meds-'+sides[i], optionsMeds);
      drawMedsSeries(chart_Med[sides[i]], meds_data[sides[i]], sides[i]);
    }
  });
</script>