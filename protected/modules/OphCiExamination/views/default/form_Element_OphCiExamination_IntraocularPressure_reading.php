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
$base_name = CHtml::modelName($value) . "[{$side}_values][{$index}]";
?>
<tr data-index="<?= $index ?>" data-side="<?= $side ?>" data-index="<?= $index ?>">
  <td>
      <?= CHtml::textField(
              "{$base_name}[reading_time]",
              $time,
              array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'cols-11')
      ) ?>
  </td>
  <td style="<?=($value->instrument && $value->instrument->scale) ? "display: none":"" ?>">
      <?= $form->dropDownList(
              $value,
              'reading_id',
              'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading',
              array(
                  'nowrapper' => true,
                  'data-base-name' => $base_name,
                  'name' => "{$base_name}[reading_id]",
                  'class' => 'cols-11',
                  'prompt' => '--'
              )
      ) ?>
  </td>
  <td class="scale_values" style="<?= (!$value->instrument || !$value->instrument->scale) ? "display: none":""?>">
      <?php if ($value->instrument && $value->instrument->scale) {
          echo $this->renderPartial(
                  '_qualitative_scale',
                  array(
                      'value' => $value,
                      'side' => $side,
                      'index' => $index,
                      'scale' => $value->instrument->scale
                  )
          );
      } ?>
  </td>
    <td>
    <input type="hidden" name="<?= $base_name ?>[instrument_id]"
           id="<?= $base_name ?>[instrument_id]" value="<?= $instrumentId ?>"/>
    <div><?= $instrumentName ?></div>
      </td>
  <td class="cols-2"><?= CHtml::hiddenField("{$base_name}[eye_id]", ($side == 'left') ? Eye::LEFT : Eye::RIGHT) ?><i
        class="oe-i trash"></i></td>
</tr>
