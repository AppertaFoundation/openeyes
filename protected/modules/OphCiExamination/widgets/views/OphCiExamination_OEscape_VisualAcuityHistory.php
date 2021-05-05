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
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oescape-plotly.js')?>"></script>
<div id="js-hs-chart-VA" class="highchart-area" data-highcharts-chart="2" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-VA.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-VFI.js')?>"></script>
  <form id="va-history-form" action="#OphCiExamination_Episode_VisualAcuityHistory" style="margin-left: 70px">
    <input name="subspecialty_id" value=<?= $this->subspecialty->id ?> type="hidden">
    <input name="patient_id" value=<?= $this->patient->id ?> type="hidden">
        <?= CHtml::dropDownList(
            'va_history_unit_id',
            $va_unit->id,
            CHtml::listData(
                OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::
                model()->active()->findAllByAttributes(array('is_near'=>0)),
                'id',
                'name'
            )
        )?>
  </form>
  <div id="plotly-VA-right" class="plotly-VA plotly-right plotly-section" data-eye-side="right"></div>
  <div id="plotly-VA-left" class="plotly-VA plotly-left plotly-section" data-eye-side="left" style="display: none;"></div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    $('#va_history_unit_id').change(function () { this.form.submit(); });
    var va_ticks = <?= CJavaScript::encode($this->getVaTicks()); ?>;
    var vfi_ticks = <?= CJavaScript::encode($this->getVfiTicks()); ?>;
    OEScape.full_va_ticks = va_ticks;
    var opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
    var laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;

    var sides = ['left', 'right'];

    var widget_count = <?= CJavaScript::encode($widget_no); ?>;
    var height = (widget_count===1)? 800: 600;

    //Plotly
    var va_plotly = <?= CJavaScript::encode($this->getPlotlyVaData()); ?>;
    var vfi_plotly = <?= CJavaScript::encode($this->getPlotlyVfiData()) ?>;

    var va_plotly_ticks = pruneYTicks(va_ticks, height, 25);
    var vfi_plotly_ticks = pruneYTicks(vfi_ticks, height, 25);

    let coloursForSide = {
        'right': '#9fec6d',
        'left': '#fe6767',
        'beo': '#e8b131'
    }

    function generateVAPlotlySeriesForSide(side)
    {
        return {
            name: 'VA('+side+')',
            x: va_plotly[side]['x'].map(function (item) {
                return new Date(item);
            }),
            y: va_plotly[side]['y'],
            line: {
                color: coloursForSide[side],
            },
            text: va_plotly[side]['x'].map(function (item, index) {
                var d = new Date(item);
                return OEScape.toolTipFormatters_plotly.VA( d, va_plotly[side]['y'][index], 'VA('+side+')');
            }),
            hoverinfo: 'text',
            hoverlabel: trace_hoverlabel,
            type: 'line',
            mode: 'lines+markers',
            marker: {
                symbol: 'circle',
                size: 10,
            },
        };
    }

    function generateVFIPlotlySeriesForSide(side) {
        return {
            name: 'VFI('+side+')',
            x: vfi_plotly[side]['x'].map(function (item) {
                return new Date(item);
            }),
            y: vfi_plotly[side]['y'],
            yaxis: 'y2',
            line: {
                color: coloursForSide[side],
                dash: 'dot',
            },
            text: vfi_plotly[side]['x'].map(function (item, index) {
                var d = new Date(item);
                return OEScape.toolTipFormatters_plotly.VFI(d, vfi_plotly[side]['y'][index], side);
            }),
            hoverinfo: 'text',
            hoverlabel: trace_hoverlabel,
            type: 'line',
            mode: 'lines+markers',
            marker: {
                symbol: 'circle',
                size: 7,
            },
        };
    }

    for (var side of sides){
      var layout_VA = JSON.parse(JSON.stringify(layout_plotly));
      layout_VA['shapes'] = [];
      layout_VA['annotations'] = [];
      setMarkingEvents_plotly(layout_VA, marker_line_plotly_options, marking_annotations, opnote_marking, side, -35, 140);
      setMarkingEvents_plotly(layout_VA, marker_line_plotly_options, marking_annotations, laser_marking, side, -35, 140);

      var data =[generateVAPlotlySeriesForSide(side), generateVAPlotlySeriesForSide('beo'), generateVFIPlotlySeriesForSide(side)];

      var yaxis_options = {
        range: [-35, 150],
        tickvals: va_plotly_ticks['tick_position'],
        ticktext: va_plotly_ticks['tick_labels'],
      };
      var yaxis2_options = {
          range: [-45, 10],
          tickvals: vfi_plotly_ticks['tick_position'],
          ticktext: vfi_plotly_ticks['tick_labels'],
      }
      layout_VA['yaxis'] = setYAxis_VA(yaxis_options);
      layout_VA['yaxis2'] = setYAxis_VFI(yaxis2_options);
      layout_VA['height'] = height;
      layout_VA['xaxis']['rangeslider'] = {};

      Plotly.newPlot(
        'plotly-VA-'+side, data, layout_VA, options_plotly
      );
    }
  });
</script>
