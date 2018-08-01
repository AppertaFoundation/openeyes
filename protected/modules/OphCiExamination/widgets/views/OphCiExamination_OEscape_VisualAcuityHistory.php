<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/highchart-VA.js')?>"></script>
  <form action="#OphCiExamination_Episode_VisualAcuityHistory">
      <?= CHtml::dropDownList('va_history_unit_id', $va_unit->id, CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->active()->findAll(), 'id', 'name'))?>
  </form>
<div id="js-hs-chart-VA" class="highchart-area" data-highcharts-chart="2" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
  <div id="highcharts-VA-right" class="highcharts-VA highcharts-right highchart-section"></div>
  <div id="highcharts-VA-left" class="highcharts-VA highcharts-left highchart-section" style="display: none;"></div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    $('#va_history_unit_id').change(function () { this.form.submit(); });
    var va_ticks = <?= CJavaScript::encode($this->getVaTicks()); ?>;
    OEScape.full_va_ticks = va_ticks;
    var axis_index = 0;
    optionsVA['yAxis'][axis_index]['tickPositions'] = va_ticks['tick_position'];
    optionsVA['yAxis'][axis_index]['labels'] = setYLabels(va_ticks['tick_position'], va_ticks['tick_labels']);
    <?php if ($widget_no == 1) {?>
    optionsVA['chart']['height'] = 800;
    <?php } ?>
    var VA_data = <?= CJavaScript::encode($this->getVaData()); ?>;
    var opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
    var laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;

    var sides = ['left', 'right'];
    var chart_VA = {};
    var plotLines = {};
    //Render the chart to get the size of the plot for tick pruning
    for (var i in sides) {
      optionsVA['xAxis']['plotLines'] = [];
      plotLines[sides[i]] = [];
      setMarkingEvents(optionsVA, opnote_marking, plotLines, sides[i]);
      setMarkingEvents(optionsVA, laser_marking, plotLines, sides[i]);
      chart_VA[sides[i]] = new Highcharts.chart('highcharts-VA-'+sides[i], optionsVA);
      drawVASeries(chart_VA[sides[i]], VA_data[sides[i]], sides[i]);
      cleanVATicks(va_ticks, optionsVA, chart_VA[sides[i]], axis_index);
    }
  });
</script>
