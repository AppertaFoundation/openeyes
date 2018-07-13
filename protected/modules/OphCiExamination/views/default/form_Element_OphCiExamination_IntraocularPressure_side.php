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
    <thead>
    <tr>
      <th class="cols-3">Time</th>
      <th width="64px">mm Hg</th>
        <?php if ($element->getSetting('show_instruments')): ?>
          <th>Instrument</th>
        <?php endif ?>
      <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($element->{"{$side}_values"} as $index => $value) {
        $this->renderPartial(
            "{$element->form_view}_reading",
            array(
                'element' => $element,
                'form' => $form,
                'side' => $side,
                'index' => $index,
                'time' => substr($value->reading_time, 0, 5),
                'value' => $value,
            )
        );
    }
    ?>
    </tbody>
  </table>
  <div id="iop-<?php echo $side; ?>-comments" class="js-comment-container field-row-pad-top" <?= (!$element->$comments) ? 'style="display: none;"' : ''; ?>>
      <?= $form->textArea($element, "{$side}_comments", array('nowrapper' => true), false,
          array(
              'class' => 'js-comment-field',
              'rows' => 1,
              'placeholder' => 'Comments',
              'style' => 'overflow-x: hidden; word-wrap: break-word;',
              'data-comment-button' => '#iop-' . $side .'-comment-button'
          )) ?>
  </div>
</div>
<div class="add-data-actions flex-item-bottom">
  <button id="iop-<?php echo $side; ?>-comment-button" type="button" class="button js-add-comments"
          data-comment-container="#iop-<?php echo $side; ?>-comments" <?= $element->$comments ? 'style="display: none;"' : ''; ?>>
    <i class="oe-i comments small-icon"></i>
  </button>
  <button type="button" class="button hint green js-add-select-search">
    <i class="oe-i plus pro-theme"></i>
  </button>
  <div id="add-to-IOP" class="oe-add-select-search" style="display: none;">
    <div class="close-icon-btn">
      <i class="oe-i remove-circle medium"></i>
    </div>
    <div class="flex-layout flex-top flex-left">
      <ul class="add-options cols-full" data-multi="false" data-clickadd="true">
          <?php foreach (CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->findAllByAttributes(['visible' => 1]), 'id', 'name') as $id => $instrument): ?>
            <li data-str="<?php echo $id; ?>">
              <span class="auto-width"><?php echo $instrument; ?></span>
            </li>
          <?php endforeach; ?>
      </ul>
    </div>
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
            'value' => new models\OphCiExamination_IntraocularPressure_Value(),
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(function () {
        var side = $('.<?= CHtml::modelName($element) ?> .<?=$side?>-eye');
        var popup = side.find('#add-to-IOP');

        function addIOPReading(selected){
            selected.removeClass('selected');
        }

        setUpAdder(
            popup,
            'return',
            addIOPReading,
            side.find('.js-add-select-search'),
            null,
            popup.find('.select-icon-btn, .close-icon-btn')
        );
    });
</script>


