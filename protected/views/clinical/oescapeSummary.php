<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
if (!empty($episode)) {
if ($episode->diagnosis) {
    $eye = $episode->eye ? $episode->eye->name : 'None';
    $diagnosis = $episode->diagnosis ? $episode->diagnosis->term : 'none';
} else {
    $eye = 'No diagnosis';
    $diagnosis = 'No diagnosis';
}

$episode->audit('episode summary', 'view');
?>
<script src="https://code.highcharts.com/stock/js/highstock.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oescape.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oes-highchart-tools.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/initStack.js')?>"></script>
    <?php $this->renderPartial('//base/_messages'); ?>
<div class="oes-left-side"  style="width: 50%;">
  <div id="charts-container" class="highchart-area <?= $episode->subspecialty->name; ?>">

    <?php $summaryItems = array();

    if ($episode->subspecialty) {
        $summaryItems = OescapeSummaryItem::model()->enabled($episode->subspecialty->id)->findAll();
    }
    if (!$summaryItems) {
        $summaryItems = OescapeSummaryItem::model()->enabled()->findAll();
    } ?>

    <?php if (count($summaryItems)) { ?>
        <?php foreach ($summaryItems as $summaryItem) {
        Yii::import("{$summaryItem->event_type->class_name}.widgets.{$summaryItem->getClassName()}");
        $widget = $this->createWidget($summaryItem->getClassName(), array(
            'episode' => $episode,
            'event_type' => $summaryItem->event_type,
        )); ?>
        <?php $widget->run_oescape();  }
    } ?>
  </div>
</div>
  <div class="oes-right-side" style="width: 50%;">
      <?php if(isset($widget)) {
        $widget->run_right_side();
      } ?>
  </div>

<?php } ?>

<script type="text/javascript">
  $(document).ready(function () {
    if ($("#charts-container").hasClass('Glaucoma')){
      $('.right-side-content').show();

      var VAChart_right = $('.highcharts-VA')[0];
      var VAChart_left = $('.highcharts-VA')[1];
      var MedChart_right = $('.highcharts-Meds')[0];
      var MedChart_left = $('.highcharts-Meds')[1];
      var IOPChart_right = $('.highcharts-IOP')[0];
      var IOPChart_left = $('.highcharts-IOP')[1];

      var max_right = Math.max($(VAChart_right).highcharts().xAxis[0].max, $(MedChart_right).highcharts().yAxis[0].max, $(IOPChart_right).highcharts().xAxis[0].max);
      var min_right =Math.min($(VAChart_right).highcharts().xAxis[0].min, $(MedChart_right).highcharts().yAxis[0].min, $(IOPChart_right).highcharts().xAxis[0].min);
      var max_left = Math.max($(VAChart_left).highcharts().xAxis[0].max, $(MedChart_left).highcharts().yAxis[0].max, $(IOPChart_left).highcharts().xAxis[0].max);
      var min_left = Math.min($(VAChart_left).highcharts().xAxis[0].min, $(MedChart_left).highcharts().yAxis[0].min, $(IOPChart_left).highcharts().xAxis[0].min);

      $(VAChart_right).highcharts().xAxis[0].setExtremes(min_right, max_right);
      $(IOPChart_right).highcharts().xAxis[0].setExtremes(min_right, max_right);
      $(MedChart_right).highcharts().yAxis[0].setExtremes(min_right, max_right);
      $(VAChart_left).highcharts().xAxis[0].setExtremes(min_left, max_left);
      $(IOPChart_left).highcharts().xAxis[0].setExtremes( min_left, max_left);
      $(MedChart_left).highcharts().yAxis[0].setExtremes( min_left, max_left);

      /**
       In order to synchronize tooltips and crosshairs, override the
       built-in events with handlers defined on the parent element.
       **/
      $('#charts-container').bind('mousemove touchmove touchstart', function (e) {
        var event_right = $(IOPChart_right).highcharts().pointer.normalize(e.originalEvent); // Find coordinates within the chart
        var point_right = $(IOPChart_right).highcharts().series[0].searchPoint(event_right, true); // Get the hovered point
        $(VAChart_right).highcharts().xAxis[0].drawCrosshair(event_right);
        $(IOPChart_right).highcharts().xAxis[0].drawCrosshair(event_right);
        $(MedChart_right).highcharts().yAxis[0].drawCrosshair(event_right);
        var event_left = $(IOPChart_left).highcharts().pointer.normalize(e.originalEvent); // Find coordinates within the chart
        var point_left = $(IOPChart_left).highcharts().series[0].searchPoint(event_left, true); // Get the hovered point
        $(VAChart_left).highcharts().xAxis[0].drawCrosshair(event_left);
        $(IOPChart_left).highcharts().xAxis[0].drawCrosshair(event_left);
        $(MedChart_left).highcharts().yAxis[0].drawCrosshair(event_left);
      });
      // VA has Navigator, use it to control all 3 charts
      Highcharts.addEvent($(VAChart_right).highcharts().xAxis[0], 'afterSetExtremes', function (e) {
        // match Extremes on other charts to VA:
        $(IOPChart_right).highcharts().xAxis[0].setExtremes( e.min, e.max);
        $(MedChart_right).highcharts().yAxis[0].setExtremes( e.min, e.max);
      });
      Highcharts.addEvent($(VAChart_left).highcharts().xAxis[0], 'afterSetExtremes', function (e) {
        // match Extremes on other charts to VA:
        $(IOPChart_left).highcharts().xAxis[0].setExtremes( e.min, e.max);
        $(MedChart_left).highcharts().yAxis[0].setExtremes( e.min, e.max);
      });
    }
  });
</script>