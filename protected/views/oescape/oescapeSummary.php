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
if (!empty($subspecialty)) { ?>
<script src="https://code.highcharts.com/stock/js/highstock.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
	<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oes-highchart-tools.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/initStack.js')?>"></script>
    <?php $this->renderPartial('//base/_messages'); ?>
<div class="oes-left-side"  style="width: 50%;">
  <div id="charts-container" class="highchart-area <?= $subspecialty->short_name; ?>">
    <?php $summaryItems = array();
        $summaryItems = OescapeSummaryItem::model()->enabled($subspecialty->id)->findAll();
    if (!$summaryItems) {
        $summaryItems = OescapeSummaryItem::model()->enabled()->findAll();
    } ?>

    <?php if (count($summaryItems)) { ?>
        <?php foreach ($summaryItems as $summaryItem) {
        Yii::import("{$summaryItem->event_type->class_name}.widgets.{$summaryItem->getClassName()}");
        $widget = $this->createWidget($summaryItem->getClassName(), array(
            'patient' => $this->patient,
            'subspecialty' => $subspecialty,
            'event_type' => $summaryItem->event_type,
        )); ?>
        <?php $widget->run_oescape(count($summaryItems));  }
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
    if ($("#charts-container").hasClass('Glaucoma')||$("#charts-container").hasClass('General Ophthalmology')){
      $('.right-side-content').show();

      var charts = [];
      charts['VA'] = [];
      charts['VA']['major_axis'] = 'xAxis';
      charts['VA']['right'] = $($('.highcharts-VA')[0]).highcharts();
      charts['VA']['left'] = $($('.highcharts-VA')[1]).highcharts();

      charts['Med'] = [];
      charts['Med']['major_axis'] = 'yAxis';
      charts['Med']['right'] = $($('.highcharts-Meds')[0]).highcharts();
      charts['Med']['left'] = $($('.highcharts-Meds')[1]).highcharts();


      charts['IOP'] = [];
      charts['IOP']['major_axis'] = 'xAxis';
      charts['IOP']['right'] = $($('.highcharts-IOP')[0]).highcharts();
      charts['IOP']['left'] = $($('.highcharts-IOP')[1]).highcharts();

      var limits = [];
      ['right', 'left'].forEach(function(eye_side)  {
        limits[eye_side] = [];
        limits[eye_side]['max'] = Object.keys(charts).reduce(function(max, chart_key) {
          var chart = charts[chart_key];
          var chart_max = chart[eye_side][chart.major_axis][0].max;
          return chart_max > max ? chart_max : max;
        }, 0);
        limits[eye_side]['min'] = Object.keys(charts).reduce(function(min, chart_key) {
          var chart = charts[chart_key];
          var chart_min = chart[eye_side][chart.major_axis][0].min;
          return chart_min < min ? chart_min : min;
        }, limits[eye_side]['max']);
      });


      for(var key in charts){
        ['right', 'left'].forEach(function(eye_side)  {
          var axis = charts[key][eye_side][charts[key].major_axis][0];
          axis.setExtremes(
            limits[eye_side].min,
            limits[eye_side].max
          );
        });
      }

      ['right', 'left'].forEach(function (eye_side) {
        var navAxis = charts.VA[eye_side].navigator.xAxis;
        navAxis.setExtremes(limits[eye_side].min, limits[eye_side].max);
      });


      /**
       In order to synchronize tooltips and crosshairs, override the
       built-in events with handlers defined on the parent element.
       **/
      $('#charts-container').bind('mousemove touchmove touchstart', function (e) {
        var event_right = charts.IOP.right.pointer.normalize(e.originalEvent); // Find coordinates within the chart
        charts.VA.right.xAxis[0].drawCrosshair(event_right);
        charts.IOP.right.xAxis[0].drawCrosshair(event_right);
        charts.Med.right.yAxis[0].drawCrosshair(event_right);
        var event_left = charts.IOP.left.pointer.normalize(e.originalEvent); // Find coordinates within the chart
        charts.VA.left.xAxis[0].drawCrosshair(event_left);
        charts.IOP.left.xAxis[0].drawCrosshair(event_left);
        charts.Med.left.yAxis[0].drawCrosshair(event_left);
      });
      // VA has Navigator, use it to control all 3 charts
      Highcharts.addEvent(charts.VA.right.xAxis[0], 'afterSetExtremes', function (e) {
        // match Extremes on other charts to VA:
        charts.IOP.right.xAxis[0].setExtremes( e.min, e.max);
        charts.Med.right.yAxis[0].setExtremes( e.min, e.max);
      });
      Highcharts.addEvent(charts.VA.left.xAxis[0], 'afterSetExtremes', function (e) {
        // match Extremes on other charts to VA:
        charts.IOP.left.xAxis[0].setExtremes( e.min, e.max);
        charts.Med.left.yAxis[0].setExtremes( e.min, e.max);
      });
    }
  });
</script>
