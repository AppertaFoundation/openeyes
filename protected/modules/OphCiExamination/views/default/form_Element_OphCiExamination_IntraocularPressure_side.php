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

use OEModule\OphCiExamination\models;
use ReferenceData;

$comments = $side . '_comments';

$readings = models\OphCiExamination_IntraocularPressure_Reading::model()->findAll();
$reading_values = [];
foreach ($readings as $reading) {
    $reading_values[$reading->name] = $reading->id;
}

?>
<div class="cols-9">
  <table id="<?= CHtml::modelName($element) . '_readings_' . $side ?>"
         class="cols-full<?php if (!$element->{"{$side}_values"}) {
                echo 'hidden "';
                         } ?>">
    <colgroup>
      <col class="cols-3">
      <col class="cols-2">
    </colgroup>
    <thead>
    <tr style="<?= count($element->{"{$side}_values"}) ? '' : 'display: none;'?>">
      <th>Time</th>
      <th>mm Hg</th>
        <?php if ($element->getSetting('show_instruments')) : ?>
          <th>Instrument</th>
        <?php endif ?>
      <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $instrument_model = OEModule\OphCiExamination\models\OphCiExamination_Instrument::model();
    foreach ($element->{"{$side}_values"} as $index => $value) {
        $this->renderPartial(
            "{$element->form_view}_reading",
            array(
                'element' => $element,
                'form' => $form,
                'side' => $side,
                'index' => $index,
                'time' => substr($value->reading_time, 0, 5),
                'instrumentId' => $value->instrument_id,
                'instrumentName' => $instrument_model->findByPk($value->instrument_id)->name,
                'value_reading_id' => isset($value->reading) ? $value->reading->id : null,
                'value_reading_name' => isset($value->reading) ? $value->reading->name : null,
                'value_qualitative_reading_id' => isset($value->qualitative_reading) ? $value->qualitative_reading->id : null,
                'value_qualitative_reading_name' => isset($value->qualitative_reading) ? $value->qualitative_reading->name : null,
                'value' => $value,
            )
        );
    }
    ?>
    </tbody>
  </table>
  <div id="iop-<?= $side; ?>-comments"
       class="comment-group js-comment-container field-row-pad-top flex-layout flex-left"
       style="<?php if (!$element->$comments && !$element->hasErrors($side.'_comments')) :
            ?>display: none;<?php
              endif; ?>"
       data-comment-button="#iop-<?= $side ?>-comment-button">
        <?= $form->textArea(
            $element,
            "{$side}_comments",
            array('nowrapper' => true),
            false,
            array(
              'class' => 'js-comment-field',
              'rows' => 1,
              'placeholder' => 'Comments',
              'style' => 'overflow-x: hidden; word-wrap: break-word;',
            )
        ) ?>
    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
  </div>
</div>

<div class="add-data-actions flex-item-bottom">
  <div class="flex-item-bottom">
    <button id="iop-<?= $side; ?>-comment-button"
            type="button"
            class="button js-add-comments"
            data-comment-container="#iop-<?= $side; ?>-comments"
            style="<?php if ($element->$comments) :
                ?>visibility: hidden;<?php
                   endif; ?>"
    >
      <i class="oe-i comments small-icon"></i>
    </button>
    <button type="button" class="button hint green js-add-select-search" data-test="add-intraocular-pressure-instrument">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
</div>

<script type="text/template" id="<?= CHtml::modelName($element) . '_reading_template_' . $side ?>" class="hidden">
    <?php
    $this->renderPartial(
        "{$element->form_view}_reading",
        array(
            'element' => $element,
            'form' => $form,
            'side' => $side,
            'index' => '{{index}}',
            'time' => '{{time}}',
            'instrument' => '{{instrument}}',
            'instrumentId' => '{{instrumentId}}',
            'instrumentName' => '{{instrumentName}}',
            'value_reading_id' => '{{value_reading_id}}',
            'value_reading_name' => '{{value_reading_name}}',
            'value_qualitative_reading_id' => '{{value_qualitative_reading_id}}',
            'value_qualitative_reading_name' => '{{value_qualitative_reading_name}}',
            'value' => new models\OphCiExamination_IntraocularPressure_Value(),
        )
    );
    ?>
</script>
<script type="text/javascript">
  $(function () {
      var side = $('.<?= CHtml::modelName($element) ?> .<?=$side?>-eye');
      var readings = JSON.parse('<?= print_r(json_encode($reading_values), 1) ?>');
      let previouslySelectedColumn = null;

      let AdderDialog = new OpenEyes.UI.AdderDialog({
          id: 'add-to-iop',
          openButton: side.find('.js-add-select-search'),
          itemSets: [
              new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                  array_map(function ($instrument) {
                      return ['label' => $instrument->name, 'id' => $instrument->id, 'scale' => isset($instrument->scale->values) ? true : false];
                  }, models\OphCiExamination_Instrument::model()->findAllAtLevels(ReferenceData::LEVEL_ALL, ['scopes' => ['active']]))
              ) ?>, {'id': 'instrument', 'header': 'Instrument'}),
              new OpenEyes.UI.AdderDialog.ItemSet([], {
                  'id': 'reading_value', 'header': 'mm Hg',
                  'splitIntegerNumberColumns': [{'min': 0, 'max': 9}, {'min': 0, 'max': 9}],
                  'style': 'display: none'
              }),
              new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                  array_map(function ($scale) {
                      return ['label' => $scale->name, 'id' => $scale->id];
                  }, models\OphCiExamination_Qualitative_Scale_Value::model()->findAllByAttributes(['scale_id' => models\OphCiExamination_Qualitative_Scale::model()->findByAttributes(['name' => 'digital'])->id]))
              ) ?>, {'id': 'scale_value', 'header': 'Scale value', 'style': 'display: none'}),
          ],
          onReturn: function (adderDialog, selectedItems) {
              if (selectedItems.length < 1
                  || selectedItems[0].scale && selectedItems.length < 2
                  || !selectedItems[0].scale && selectedItems.length < 3) {
                  return false;
              }

              // show header after adding new values
              side.find('thead tr').show();


              let value_reading_id = null;
              let value_reading_name = null;
              if (!selectedItems[0].scale) {
                  let value_reading = 0;
                  for (let i = 1; i < selectedItems.length; i++) {
                      if (selectedItems[i].reading_value == null) {
                          return false;
                      }
                      value_reading = 10 * value_reading + parseInt(selectedItems[i].reading_value);
                  }
                  value_reading_id = readings[value_reading];
                  value_reading_name = value_reading;
              }

              if (selectedItems[0].scale && (selectedItems[1]['id'] == null || selectedItems[1]['label'] == null)) {
                  return false;
              }
              let value_qualitative_reading_id = selectedItems[0].scale ? selectedItems[1]['id'] : null;
              let value_qualitative_reading_name = selectedItems[0].scale ? selectedItems[1]['label'] : null;

              OphCiExamination_IntraocularPressure_addReading(
                  '<?=$side?>',
                  selectedItems[0]['id'],
                  selectedItems[0]['label'],
                  value_reading_id,
                  value_reading_name,
                  value_qualitative_reading_id,
                  value_qualitative_reading_name,
              );

              // hide reading_value and scale_value columns
              adderDialog.toggleColumnById(['reading_value', 'scale_value'], false);
              previouslySelectedColumn = null;
              return true;
          },
      });

      // show / hide reading value column and scale value column
      $('.<?= CHtml::modelName($element) ?> .<?=$side?>-eye').on('click', 'ul[data-id="instrument"] li', function() {
          if ($(this).hasClass("selected")) {
              if ($(this).data('scale')) {
                  AdderDialog.toggleColumnById(['reading_value'], false);
                  AdderDialog.toggleColumnById(['scale_value'], true);

                  if (previouslySelectedColumn === 'reading_value') {
                      AdderDialog.removeSelectedColumnById(['reading_value', 'scale_value']);
                      // select the first option as defaul
                      side.find('ul[data-id="scale_value"] li').first().click();
                  }
                  if (!previouslySelectedColumn) {
                      side.find('ul[data-id="scale_value"] li').first().click();
                  }
                  previouslySelectedColumn = "scale_value";
              } else {
                  AdderDialog.toggleColumnById(['reading_value'], true);
                  AdderDialog.toggleColumnById(['scale_value'], false);

                  if (previouslySelectedColumn === 'scale_value') {
                      AdderDialog.removeSelectedColumnById(['reading_value', 'scale_value']);
                      // select the first option as default
                      side.find('ul[data-id="reading_value"] li').first().click();
                  }
                  if (!previouslySelectedColumn) {
                      side.find('ul[data-id="reading_value"] li').first().click();
                  }
                  previouslySelectedColumn = "reading_value";
              }
          } else {
              AdderDialog.toggleColumnById(['reading_value'], false);
              AdderDialog.toggleColumnById(['scale_value'], false);
              AdderDialog.removeSelectedColumnById(['reading_value', 'scale_value']);
              previouslySelectedColumn = null;
          }
      });

      let default_instrument_id = <?= models\Element_OphCiExamination_IntraocularPressure::model()->getSetting('default_instrument_id') ?>;
      side.find('.js-add-select-search').on('click', function() {
          let $first_instrument_li = null;
          if (default_instrument_id) {
              // select the default instrument
              $first_instrument_li = side.find('ul[data-id="instrument"] li[data-id=' + default_instrument_id + ']').first();
          } else {
              // select the first instrument by default
              $first_instrument_li = side.find('ul[data-id="instrument"] li').first().click();
          }
          if (!$first_instrument_li.hasClass('selected')) {
              $first_instrument_li.click();
          }
      });
  });

  function OphCiExamination_IntraocularPressure_addReading(side, instrumentId, instrumentName,
               value_reading_id, value_reading_name, value_qualitative_reading_id, value_qualitative_reading_name) {
      var table = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_" + side);
      var indices = table.find('tr').map(function () {
          return $(this).data('index');
      });

      let tr = Mustache.render(
          template = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_reading_template_" + side).text(),
          {
              index: indices.length ? Math.max.apply(null, indices) + 1 : 0,
              time: (new Date).toTimeString().substr(0, 5),
              instrumentId: instrumentId,
              instrumentName: instrumentName,
              value_reading_id: value_reading_id,
              value_reading_name: value_reading_name,
              value_qualitative_reading_id: value_qualitative_reading_id,
              value_qualitative_reading_name: value_qualitative_reading_name,
          }
      );
      table.find("tbody").append(tr);

      // hide value reading column
      if (!value_reading_id) {
          table.find("tbody tr:last").find('input[name*="[reading_id]"]').parent().remove();
      }
      // hide qualitative reading column
      if (!value_qualitative_reading_id) {
          table.find("tbody tr:last").find('input[name*="[qualitative_reading_id]"]').parent().remove();
      }

      table.show();
  }
</script>


