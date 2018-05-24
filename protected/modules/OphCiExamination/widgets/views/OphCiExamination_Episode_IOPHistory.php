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
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/highchart-IOP.js')?>"></script>

<div id="js-hs-chart-IOP" class="highchart-area" data-highcharts-chart="1" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
  <div id="highcharts-IOP-right" class="highcharts-IOP highcharts-right highchart-section"></div>
  <div id="highcharts-IOP-left" class="highcharts-IOP highcharts-left highchart-section" style="display: none;"></div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    var IOP_data = <?= CJavaScript::encode($this->getIOPData()); ?>;
    var IOP_target = <?= CJavaScript::encode($this->getTargetIOP()); ?>;
    var IOP_marking = <?= CJavaScript::encode($this->getIOPMarkingEvent()); ?>;
    var sides = ['left', 'right'];
    var chart_IOP = {}, Yaxis = {};
    for (var i in sides) {
      optionsIOP['xAxis']['plotLines'] = [];
      for($marking_key in IOP_marking[sides[i]]){
        for ($i in IOP_marking[sides[i]][$marking_key]){
          optionsIOP['xAxis']['plotLines'].push(setXPlotLine($marking_key,IOP_marking[sides[i]][$marking_key][$i], sides[i] ));
        }
      }
      Yaxis[sides[i]] = setYPlotline(IOP_target, sides[i]);
      optionsIOP['yAxis']['plotLines'] = [Yaxis[sides[i]]];
      chart_IOP[sides[i]] = Highcharts.chart('highcharts-IOP-'+sides[i], optionsIOP);
      drawIOPSeries(chart_IOP[sides[i]], IOP_data[sides[i]], sides[i]);
    }
  });
</script>
