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
    if ($("#charts-container").hasClass('Glaucoma')||$("#charts-container").hasClass('General')){
      $('.right-side-content').show();

      var charts = [];
      charts['VA'] = [];
      charts['VA']['right'] = $('.plotly-VA')[0];
      charts['VA']['left'] = $('.plotly-VA')[1];

      charts['Med'] = [];
      charts['Med']['right'] = $('.plotly-Meds')[0];
      charts['Med']['left'] = $('.plotly-Meds')[1];


      charts['IOP'] = [];
      charts['IOP']['right'] = $('.plotly-IOP')[0];
      charts['IOP']['left'] = $('.plotly-IOP')[1];


      var limits = {};
      ['right', 'left'].forEach(function(eye_side)  {
        limits[eye_side] = {};
        limits[eye_side]['min'] = Object.keys(charts).reduce(function(min, chart_key) {
          var chart = charts[chart_key];
          var chart_min = chart[eye_side]['layout']['xaxis']['range'][0];
          return new Date(chart_min) < min ? new Date(chart_min) : min;
        }, new Date());
        limits[eye_side]['max'] = Object.keys(charts).reduce(function(max, chart_key) {
          var chart = charts[chart_key];
          var chart_max = chart[eye_side]['layout']['xaxis']['range'][1];
          return new Date(chart_max) > max ? new Date(chart_max) : max;
        }, limits[eye_side]['min']);

        for(var key in charts){
            Plotly.relayout(charts[key][eye_side], 'xaxis.range', [limits[eye_side].min, limits[eye_side].max]);
          if (key==='IOP'){
            var index = charts[key][eye_side].layout.shapes.length-1;
            Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x0', limits[eye_side].min);
            Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x1', limits[eye_side].max);
            Plotly.relayout(charts[key][eye_side], 'annotations['+index+'].x', limits[eye_side].min);
          }
        }

      });

      $('.rangeslider-container').on('mouseenter mouseover', function (e) {
        var chart_VA = $(this).parents('.plotly-VA')[0];
        var eye_side = $(chart_VA).attr('data-eye-side');
        var current_range = chart_VA['layout']['xaxis']['range'];

        var chart_list = $('.plotly-'+eye_side);
        for (var i=0; i < chart_list.length; i++){
          Plotly.relayout(chart_list[i], 'xaxis.range', current_range);
        }
      });
    }

    var plots = $('.plotly-section');
    for (var j = 0; j < plots.length; j++) {
      function get_hover_func(index){
        return function (data) {
          var pn = '', tn = '';
          for (var i = 0; i < data.points.length; i++) {
            pn = data.points[i].pointNumber;
            tn = data.points[i].curveNumber;
            size = data.points[i].data.marker.size;
          }
          var sizes = new Array(plots[index].data[tn].x.length).fill(10);
          sizes[pn] = 15;
          var update = {'marker': {size: sizes}};
          Plotly.restyle(plots[index], update, [tn]);
        }
      }

      function get_unhover_func(index){
        return function (data) {
          var pn='', tn='';
          for(var i=0; i < data.points.length; i++){
            pn = data.points[i].pointNumber;
            tn = data.points[i].curveNumber;
          }
          var update = {'marker':{size:10}};
          Plotly.restyle(plots[index], update, [tn]);
        }
      }
      if (!$(plots[j]).hasClass('plotly-Meds')){
        plots[j].on('plotly_hover', get_hover_func(j));
        plots[j].on('plotly_unhover', get_unhover_func(j));
      }
    }
  });

</script>