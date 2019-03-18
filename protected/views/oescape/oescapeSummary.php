
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

    //hide cursors in plot
    ['right', 'left'].forEach(function (eye_side) {
      for(var key in charts){
        $(charts[key][eye_side]).find('.cursor-crosshair, .cursor-ew-resize').css("cursor", 'none');
      }
      $('.plotly-MR').find('.cursor-crosshair, .cursor-ew-resize').css("cursor", 'none');
    });

    if ($("#charts-container").hasClass('Glaucoma')||$("#charts-container").hasClass('General')){
      $('.right-side-content').show();

      var limits = {};
      ['right', 'left'].forEach(function(eye_side)  {
        limits[eye_side] = {};
        limits[eye_side]['min'] = Object.keys(charts).reduce(function(min, chart_key) {
          var chart = charts[chart_key];
          var chart_data_list = chart[eye_side]['data'];
          var has_data = false;
          for (var i in chart_data_list){
            if(chart_data_list[i]['x'].length!==0){
              has_data = true;
            }
          }
          var chart_min = chart[eye_side]['layout']['xaxis']['range'][0];
          return has_data && new Date(chart_min) < min ? new Date(chart_min) : min;
        }, new Date());
        limits[eye_side]['max'] = Object.keys(charts).reduce(function(max, chart_key) {
          var chart = charts[chart_key];
          var chart_data_list = chart[eye_side]['data'];
          var has_data = false;
          for (var i in chart_data_list){
            if(chart_data_list[i]['x'].length!==0){
              has_data = true;
            }
          }
          var chart_max = chart[eye_side]['layout']['xaxis']['range'][1];
          return has_data && new Date(chart_max) > max ? new Date(chart_max) : max;
        }, limits[eye_side]['min']);
        if (limits[eye_side]['min']!==limits[eye_side]['max']){
          for(var key in charts){
            Plotly.relayout(charts[key][eye_side], 'xaxis.range', [limits[eye_side].min, limits[eye_side].max]);

            if (key==='IOP'){
              //set the iop target line
              var index = charts[key][eye_side].layout.shapes.length-1;
              if (index>=0 && charts[key][eye_side].layout.shapes[index].y0 == charts[key][eye_side].layout.shapes[index].y1){
                Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x0', limits[eye_side].min);
                Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x1', limits[eye_side].max);
                Plotly.relayout(charts[key][eye_side], 'annotations['+index+'].x', limits[eye_side].min);
              }
            }
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
  });

</script>