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

$comments = $side . '_comments';

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
    <tr>
      <th>Time</th>
      <th>mm Hg</th>
        <?php if ($element->getSetting('show_instruments')): ?>
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
                'value' => $value,
            )
        );
    }
    ?>
    </tbody>
  </table>
  <div id="iop-<?= $side; ?>-comments"
       class="comment-group js-comment-container field-row-pad-top flex-layout flex-left"
       style="<?php if (!$element->$comments): ?>display: none;<?php endif; ?>"
       data-comment-button="#iop-<?= $side ?>-comment-button">
      <?= $form->textArea($element, "{$side}_comments", array('nowrapper' => true), false,
          array(
              'class' => 'js-comment-field',
              'rows' => 1,
              'placeholder' => 'Comments',
              'style' => 'overflow-x: hidden; word-wrap: break-word;',
          )) ?>
    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
  </div>
</div>

<div class="add-data-actions flex-item-bottom">
  <div class="flex-item-bottom">
    <button id="iop-<?= $side; ?>-comment-button"
            type="button"
            class="button js-add-comments"
            data-comment-container="#iop-<?= $side; ?>-comments"
            style="<?php if ($element->$comments): ?>visibility: hidden;<?php endif; ?>"
    >
      <i class="oe-i comments small-icon"></i>
    </button>
    <button type="button" class="button hint green js-add-select-search">
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
            'value' => new models\OphCiExamination_IntraocularPressure_Value(),
        )
    );
    ?>
</script>
<script type="text/javascript">
  $(function () {
    var side = $('.<?= CHtml::modelName($element) ?> .<?=$side?>-eye');

    new OpenEyes.UI.AdderDialog({
      id: 'add-to-iop',
      openButton: side.find('.js-add-select-search'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($instrument) {
                return ['label' => $instrument->name, 'id' => $instrument->id];
            },
                OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->findAllByAttributes(['visible' => 1]))
        ) ?>, {'multiSelect': true})],
        onReturn: function (adderDialog, selectedItems) {
            for (let i = 0; i < selectedItems.length; i++) {
                OphCiExamination_IntraocularPressure_addReading(
                    '<?=$side?>',
                    selectedItems[i]['id'],
                    selectedItems[i]['label']);
                let $table = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_" + '<?=$side?>');
                let $table_row = $table.find("tr:last");
                let $scale_td = $table_row.find("td.scale_values");
                let index = $table_row.data('index');

                getScaleDropdown(selectedItems[i]['id'], $scale_td, index, '<?=$side?>');
            };
            return true;
        },
    });
  });

  function OphCiExamination_IntraocularPressure_addReading(side, instrumentId, instrumentName) {
      var table = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_" + side);
      var indices = table.find('tr').map(function () {
          return $(this).data('index');
      });

      table.find("tbody").append(
          Mustache.render(
              template = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_reading_template_" + side).text(),
              {
                  index: indices.length ? Math.max.apply(null, indices) + 1 : 0,
                  time: (new Date).toTimeString().substr(0, 5),
                  instrumentId: instrumentId,
                  instrumentName: instrumentName
              }
          )
      );

      table.show();
  }
</script>


