<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/highchart-MR.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oescape-plotly.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-MR.js')?>"></script>


<div id="js-hs-chart-MR" class="highchart-area" data-highcharts-chart="0" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
  <div id="highcharts-MR-right" class="highcharts-MR highcharts-right highchart-section"></div>
  <div id="highcharts-MR-left" class="highcharts-MR highcharts-left highchart-section" style="display: none;"></div>
  <div style="z-index:10; position: relative; width: 150px; top: -800px;">
    <form action="#OphCiExamination_Episode_MedicalRetinalHistory" >
      <input name="subspecialty_id" value=<?= $this->subspecialty->id ?> type="hidden">
      <input name="patient_id" value=<?= $this->patient->id ?> type="hidden">
      <?= CHtml::dropDownList(
            'mr_history_va_unit_id',
            $va_unit->id,
            CHtml::listData(
              OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->active()->findAll(),
                'id',
                'name')
      ) ?>
    </form>
  </div>
</div>
<div class="oes-data-row-input">
</div>

<script type="text/javascript">
  $(document).ready(function () {
    //right side image
    var doc_list = <?= CJavaScript::encode($this->getDocument()); ?>;
    setImgStack($('#oct-stack'), 'oct_img_',  doc_list['right'].length?doc_list['right'][0]['doc_id']:null );
    setImgStack($('#oct-stack'), 'oct_img_',  doc_list['left'].length?doc_list['left'][0]['doc_id']:null );
    //left side plots
    $('#mr_history_va_unit_id').change(function () { this.form.submit(); });
    var axis_index = 1;
    var va_axis = <?= CJavaScript::encode($this->getVaAxis()); ?>;
    options_MR['yAxis'][axis_index]['title']['text'] = "VA ("+va_axis+")";
    var va_ticks = <?= CJavaScript::encode($this->getVaTicks()); ?>;
    OEScape.full_va_ticks = va_ticks;
    options_MR['yAxis'][axis_index]['tickPositions'] = va_ticks['tick_position'];
    options_MR['yAxis'][axis_index]['labels'] = setYLabels(va_ticks['tick_position'], va_ticks['tick_labels']);
    var injections_data = <?= CJavaScript::encode($this->getInjectionsList()); ?>;
    var VA_data = <?= CJavaScript::encode($this->getVaData()); ?>;

    var CRT_data = <?= CJavaScript::encode($this->getCRTData()); ?>;

    var VA_lines_data = <?= CJavaScript::encode($this->getLossLetterMoreThan5()); ?>;
    var opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
    var laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;

    var sides = ['left', 'right'];
    var chart_MR = {};
    var plotLines = {};
    for (var i in sides) {
      changeSetting(injections_data, sides[i]);
      options_MR['xAxis']['plotLines'] = [];
      plotLines[sides[i]] = [];
      setMarkingEvents(options_MR, opnote_marking, plotLines, sides[i]);
      setMarkingEvents(options_MR, laser_marking, plotLines, sides[i]);
      chart_MR[sides[i]] = new Highcharts.chart('highcharts-MR-'+sides[i], options_MR);
      drawMRSeries(chart_MR[sides[i]], VA_data, CRT_data, VA_lines_data, injections_data,va_axis);
      cleanVATicks(va_ticks, options_MR, chart_MR[sides[i]], axis_index);
    }


    //plotly
    var va_plotly = <?= CJavaScript::encode($this->getPlotlyVaData()); ?>;
    var crt_plotly = <?= CJavaScript::encode($this->getPlotlyCRTData()); ?>;

    var va_plotly_ticks = pruneYTicks(va_ticks, 800, 17);


    for (var side of sides){

      layout_plotly['shapes'] = [];
      layout_plotly['annotations'] = [];
      layout_plotly['yaxis'] = setYAxis_MR(va_yaxis);
      layout_plotly['yaxis']['tickvals'] = va_plotly_ticks['tick_position'];
      layout_plotly['yaxis']['ticktext'] = va_plotly_ticks['tick_labels'];
      layout_plotly['xaxis']['rangeslider'] = {};

      setMarkingEvents_plotly(layout_plotly, marker_line_plotly_options, marking_annotations, opnote_marking, side);
      setMarkingEvents_plotly(layout_plotly, marker_line_plotly_options, marking_annotations, laser_marking, side);


      var data =[{
        name: 'VA('+side+')',
        x: va_plotly[side]['x'].map(function (item) {
          return new Date(item);
        }),
        y: va_plotly[side]['y'],
        line: {
          color: (side=='right')?'#9fec6d':'#fe6767',
        },
        text: va_plotly[side]['x'].map(function (item, index) {
          return OEScape.toolTipFormatters_plotly.VA(new Date(item), va_plotly[side]['y'][index], 'VA('+side+')');
        }),
        hoverinfo: 'text',
        type: 'line',
      }, {
        name: 'CRT('+side+')',
        x: crt_plotly[side]['x'].map(function (item) {
          return new Date(item);
        }),
        y: crt_plotly[side]['y'],
        line: {
          color: (side=='right')?'#9fec6d':'#fe6767',
        },
        text: crt_plotly[side]['x'].map(function (item, index) {
          return new Date(item)+'<br>CRT(' + side + '):' + crt_plotly[side]['y'][index];
        }),
        hoverinfo: 'text',
        yaxis: 'y2',
        type: 'line',
        line: {
          dash: 'dot',
        }
      }];

      if(!crt_plotly[side]['y'].length) {
        crt_yaxis['range'] = [250, 600];
        crt_yaxis['tick0'] = 250;
      } else {
        crt_yaxis['range'] = [Math.min.apply(Math, crt_plotly[side]['y']), Math.max.apply(Math, crt_plotly[side]['y'])+20];
        crt_yaxis['tick0'] = Math.min.apply(Math, crt_plotly[side]['y']);
      }
      crt_yaxis['dtick'] = 10;
      layout_plotly['yaxis2'] = setYAxis_MR(crt_yaxis);


      var j = Object.keys(injections_data[side]).length+1;
      flags_yaxis['range'] = [0, 20*j];
      flags_yaxis['domain'] = [0, 0.05*j];
      layout_plotly['yaxis3'] = setYAxis_MR(flags_yaxis);

      var text = {
        x:[],
        y:[],
        text:[],
        yaxis: 'y3',
        mode:'text',
      };

      //Set the flags for injections
      for (var key in injections_data[side]){
        for (var i in injections_data[side][key]) {
          text['x'].push(new Date(injections_data[side][key][i]['x']));
          text['y'].push(crt_yaxis['tick0'] -20 * j);
          text['text'].push(key);

          var inj_shape = {
            x0: new Date(injections_data[side][key][i]['x']),
            y0: 20 * j,
            x1: new Date(injections_data[side][key][i]['x'] + 86400000 * 10),
            y1: 20 * (j - 0.5),
            color: (side == 'right') ? '#9fec6d' : '#fe6767',
            yaxis: 'y3',
          };
          layout_plotly['shapes'].push(setMRFlags_options(inj_shape));
        }
        j--;
      }

      //set the flags for letters >5

      for (var i in VA_lines_data[side]) {
        text['x'].push(new Date(VA_lines_data[side][i]['x']));
        text['y'].push(crt_yaxis['tick0'] - 20*j);
        text['text'].push('>5');

        var line_shape = {
          x0: new Date(VA_lines_data[side][i]['x']),
          y0: 20*j,
          x1: new Date(VA_lines_data[side][i]['x'] + 86400000 * 10),
          y1: 20*(j - 0.5),
          color: (side == 'right') ? '#9fec6d' : '#fe6767',
          yaxis: 'y3',
        };
        layout_plotly['shapes'].push(setMRFlags_options(line_shape));
      }
      j--;


      data.push(text);

      Plotly.newPlot(
        'highcharts-MR-'+side, data, layout_plotly, options_plotly
      );
    }
  });
</script>
