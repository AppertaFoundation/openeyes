
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
<style>
  div.plotly-notifier {
    visibility: hidden;
  }
</style>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/initStack.js')?>"></script>
    <?php $this->renderPartial('//base/_messages'); ?>
<div class="oes-left-side"  style="width: 50%;">
  <div id="charts-container" class="highchart-area <?= $subspecialty->short_name; ?>">
    <?php $summaryItems = array();
        $summaryItems = OescapeSummaryItem::model()->enabled($subspecialty->id)->findAll();
    if (!$summaryItems) {
        $summaryItems = OescapeSummaryItem::model()->enabled()->findAll();
    } ?>
    <div id='oes-side-indicator' style="">
      <button class="selected plot-display-label reset-zoom cols-2">Reset Zoom Level</button>
      <h4 id='oes-side-indicator-left' class='cols-7' style="color:#fe6767;
      text-align: center;
      font-weight: 500;
      display:none;">
      Left
      </h4>
      <h4 id='oes-side-indicator-right' class='cols-7' style="color:#9fec6d;
      text-align: center;
      font-weight: 500;
      display:inline-block;">

      Right
      </h4>
    </div>
    <?php if (count($summaryItems)) { ?>
        <?php foreach ($summaryItems as $summaryItem) {
            Yii::import("{$summaryItem->event_type->class_name}.widgets.{$summaryItem->getClassName()}");
            $widget = $this->createWidget($summaryItem->getClassName(), array(
            'patient' => $this->patient,
            'subspecialty' => $subspecialty,
            'event_type' => $summaryItem->event_type,
            )); ?>
            <?php $widget->run_oescape(count($summaryItems));
        }
    } ?>
  </div>
</div>
  <div class="oes-right-side" style="width: 50%;">
    <?php
    if (isset($widget)) {
            $widget->run_right_side();
    } ?>
  </div>

<?php } ?>

<script type="text/javascript">
  // init min and max
  let min_value = new Date();
  let max_value = new Date();

  $(document).ready(function () {
  //set min and max
  eye_side = $('.js-oes-eyeside.selected').data('side');
  switch(eye_side){
    case 'left':
    min_value = new Date($('.plotly-left')[0]['layout']['xaxis']['range'][0]);
    max_value = new Date($('.plotly-left')[0]['layout']['xaxis']['range'][1]);
    break;
    case 'right':
    default:
    min_value = new Date($('.plotly-right')[0]['layout']['xaxis']['range'][0]);
    max_value = new Date($('.plotly-right')[0]['layout']['xaxis']['range'][1]);
    break;
  }

    let charts = [];
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
      for(let key in charts){
        $(charts[key][eye_side]).find('.cursor-crosshair, .cursor-ew-resize').css("cursor", 'none');
      }
      $('.plotly-MR').find('.cursor-crosshair, .cursor-ew-resize').css("cursor", 'none');
    });

    if ($("#charts-container").hasClass('Glaucoma') || $("#charts-container").hasClass('General')){
      $('.right-side-content').show();

      let limits = {};
      ['right', 'left'].forEach(function(eye_side)  {

				limits[eye_side] = {};
        limits[eye_side].min = Object.keys(charts).reduce(function(min, chart_key) {
          let chart = charts[chart_key];
          let chart_data_list = chart[eye_side]['data'];
          let has_data = false;
          for (let i in chart_data_list){
            if(chart_data_list[i]['x'].length!==0){
              has_data = true;
            }
          }
          let chart_min = chart[eye_side]['layout']['xaxis']['range'][0];
          return has_data && new Date(chart_min) < min ? new Date(chart_min) : min;
        }, new Date());

        limits[eye_side].max = Object.keys(charts).reduce(function(max, chart_key) {
          let chart = charts[chart_key];
          let chart_data_list = chart[eye_side]['data'];
          let has_data = false;
          for (let i in chart_data_list){
            if(chart_data_list[i]['x'].length !== 0){
              has_data = true;
            }
          }
          let chart_max = chart[eye_side]['layout']['xaxis']['range'][1];
          return has_data && new Date(chart_max) > max ? new Date(chart_max) : max;
        }, limits[eye_side].min );

          if (limits[eye_side].min !== limits[eye_side].max){
          for(let key in charts){
            Plotly.relayout(charts[key][eye_side], 'xaxis.range', [limits[eye_side].min, limits[eye_side].max]);

            if (key==='IOP'){
              //set the iop target line
              let index = charts[key][eye_side].layout.shapes.length-1;
              if (index>=0 && charts[key][eye_side].layout.shapes[index].y0 == charts[key][eye_side].layout.shapes[index].y1){
                Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x0', limits[eye_side].min);
                Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x1', limits[eye_side].max);
                Plotly.relayout(charts[key][eye_side], 'annotations['+index+'].x', limits[eye_side].min);
              }
            }
          }
        }

      });

      // $( ".reset-zoom" ).trigger( "click" );

      $('.plotly-right, .plotly-left').on('mouseenter mouseover', function (e) {
        // let chart = $(this)[0];
        // if($(this).hasClass('plotly-right')||$(this).hasClass('plotly-left')){
        //   let eye_side = $(chart).attr('data-eye-side');
        //   let chart_list = $('.plotly-'+eye_side);
				//
        //   // init locals
        //   let my_min_value = new Date(chart_list[0]['layout']['xaxis']['range'][0]);
        //   let my_max_value = new Date(chart_list[0]['layout']['xaxis']['range'][1]);
        //   //set min max
        //   for (let i=0; i < chart_list.length; i++){
        //   //test min
        //   if(my_min_value<chart_list[i]['layout']['xaxis']['range'][0])
        //   my_min_value = new Date(chart_list[i]['layout']['xaxis']['range'][0]);
        //   //test max
        //   if(my_min_value>chart_list[i]['layout']['xaxis']['range'][1])
        //   my_max_value = new Date(chart_list[i]['layout']['xaxis']['range'][1]);
        //   }
        //   // set these ranges to the min and max values
        //   let current_range = [my_min_value, my_max_value];
        //   // end
        //   for (let i=0; i < chart_list.length; i++){
        //     Plotly.relayout(chart_list[i], 'xaxis.range', current_range);
        //   }
        // }
      });
    }
  });
  //get all reset buttons
  var els = document.getElementsByClassName('reset-zoom');

  Array.prototype.forEach.call(els, function(el) {
    // for each reset button
    el.addEventListener('click', function () {
      //are we looking at the left eye
      // let chart_list;
      // eye_side = $('.js-oes-eyeside.selected').data('side');
      // if(eye_side == 'both'){
      //   chart_list = $('.plotly-left, .plotly-right');
      // }
      // else{
      //   chart_list = $('.plotly-'+eye_side);
      // }
      // //reset the graphs to basics before we st them to their maximums
      // for (let i=0; i < chart_list.length; i++){
      //   Plotly.relayout(chart_list[i], 'xaxis.autorange', true);
      // }
			//
      // let min_date = new Date(chart_list[0]['layout']['xaxis']['range'][0]);
      // let max_date = new Date(chart_list[0]['layout']['xaxis']['range'][1]);
			//
      // //set min max
      // for (let i=0; i < chart_list.length; i++)
      // {
			// 	//test min
			// 	if(min_date<chart_list[i]['layout']['xaxis']['range'][0])
			// 	min_date = new Date(chart_list[i]['layout']['xaxis']['range'][0]);
			// 	//test max
			// 	if(min_date>chart_list[i]['layout']['xaxis']['range'][1])
			// 	max_date = new Date(chart_list[i]['layout']['xaxis']['range'][1]);
      // }
      // min_date.setDate(min_date.getDate() - 15);
      // max_date.setDate(max_date.getDate() + 15);
			//
      // console.log("Chart list: " + chart_list);
			//
			// console.log("Chart:");
			// console.log(chart_list[0]);
			//
			// console.log("Getting chart minmaxes");
			// console.log("min: " + min_date);
			// console.log("max: " + max_date);
			//
      // // set these new ranges
      // let current_range = [min_date, max_date];
      // for (let i=0; i < chart_list.length; i++){
      //   Plotly.relayout(chart_list[i], 'xaxis.range', current_range);
      // }

        let charts = [];
        charts['VA'] = [];
        charts['VA']['right'] = $('.plotly-VA')[0];
        charts['VA']['left'] = $('.plotly-VA')[1];

        charts['Med'] = [];
        charts['Med']['right'] = $('.plotly-Meds')[0];
        charts['Med']['left'] = $('.plotly-Meds')[1];

        charts['IOP'] = [];
        charts['IOP']['right'] = $('.plotly-IOP')[0];
        charts['IOP']['left'] = $('.plotly-IOP')[1];

        let limits = {};
        ['right', 'left'].forEach(function(eye_side)  {

            // for(let key in charts){
            //     Plotly.relayout(charts[key][eye_side], {'xaxis.autrange': true});
						// }

            let min_data_points_read = [];
            let max_data_points_read = [];

            limits[eye_side] = {};
            limits[eye_side].min = Object.keys(charts).reduce(function(min, chart_key) {

								console.log("Initial min: " + min);

                let chart = charts[chart_key];
                let chart_data_list = chart[eye_side]['data'];
                let has_data = false;
                for (let i in chart_data_list){
                    if(chart_data_list[i]['x'].length!==0){
                        has_data = true;
                        min_data_points_read.push(chart_data_list[i]['x']);
                    }
                }
                let chart_min = chart[eye_side]['layout']['xaxis']['range'][0];
                console.log("Accumulator min value: " + min);
                console.log("Chart min for chart: " + chart_min);

                return has_data && new Date(chart_min) < min ? new Date(chart_min) : min;
            }, new Date());

            console.log("Got minimum ");
            console.log(limits[eye_side].min);

            limits[eye_side].max = Object.keys(charts).reduce(function(max, chart_key) {
                console.log("Initial max: " + max);

                let chart = charts[chart_key];
                let chart_data_list = chart[eye_side]['data'];
                let has_data = false;
                for (let i in chart_data_list){
                    if(chart_data_list[i]['x'].length !== 0){
                        has_data = true;
                        max_data_points_read.push(chart_data_list[i]['x']);
                    }
                }
                let chart_max = chart[eye_side]['layout']['xaxis']['range'][1];
                console.log("Accumulator max value: " + max);
                console.log("Chart max for chart: " + chart_max);

                return has_data && new Date(chart_max) > max ? new Date(chart_max) : max;
            }, limits[eye_side].min );

            console.log("Got maximum ");
            console.log(limits[eye_side].max);

            console.log('Min data points read: ');
            console.log(min_data_points_read);
            console.log('Max data points read: ');
            console.log(max_data_points_read);

            if (limits[eye_side].min !== limits[eye_side].max){
                for(let key in charts){
                    console.log("Attempting to relayout the range to [" + limits[eye_side].min + ", " + limits[eye_side].max + "]")

										let updateParams = {
                        'xaxis.range': [limits[eye_side].min, limits[eye_side].max]
                    };

                    console.log("Update parameters are: ");
										console.log(updateParams);

										Plotly.relayout(charts[key][eye_side], updateParams);

										//Commenting out conflating factor - this is temporary
                    // if (key==='IOP'){
                    //     //set the iop target line
                    //     let index = charts[key][eye_side].layout.shapes.length-1;
                    //     if (index>=0 && charts[key][eye_side].layout.shapes[index].y0 == charts[key][eye_side].layout.shapes[index].y1){
                    //         Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x0', limits[eye_side].min);
                    //         Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x1', limits[eye_side].max);
                    //         Plotly.relayout(charts[key][eye_side], 'annotations['+index+'].x', limits[eye_side].min);
                    //     }
                    // }
                    Plotly.relayout(charts[key][eye_side], updateParams);

                }
            }

        });
    })
  });
</script>