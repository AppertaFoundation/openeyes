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
<form action="#OphCiExamination_Episode_MedicalRetinalHistory">
    <?= CHtml::dropDownList(
        'mr_history_va_unit_id',
        $va_unit->id,
        CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->active()->findAll(),
            'id',
            'name')) ?>
</form>
<div id="js-hs-chart-MR" class="highchart-area" data-highcharts-chart="0" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
  <div id="highcharts-MR-right" class="highcharts-MR highcharts-right highchart-section"></div>
  <div id="highcharts-MR-left" class="highcharts-MR highcharts-left highchart-section" style="display: none;"></div>
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
    options_MR['yAxis'][axis_index]['tickPositions'] = va_ticks['tick_position'];
    options_MR['yAxis'][axis_index]['labels'] = setYLabels(va_ticks['tick_position'], va_ticks['tick_labels']);
    var injections_data = <?= CJavaScript::encode($this->getInjectionsList()); ?>;
    var VA_data = <?= CJavaScript::encode($this->getVaData()); ?>;
    var CRT_data = <?= CJavaScript::encode($this->getCRTData()); ?>;
    var VA_lines_data = <?= CJavaScript::encode($this->getLossLetterMoreThan5()); ?>;
    var sides = ['left', 'right'];
    var chart_MR = {};
    for (var i in sides) {
      changeSetting(injections_data, sides[i]);
      options_MR['title']['text']="Retinal thickness-Visual acuity ("+sides[i]+" Eye)";
      chart_MR[sides[i]] = new Highcharts.chart('highcharts-MR-'+sides[i], options_MR);
      drawMRSeries(chart_MR[sides[i]], VA_data, CRT_data, VA_lines_data, injections_data,va_axis);
    }

    cleanVATicks(va_ticks, options_MR, chart_MR, axis_index);
  });
</script>
