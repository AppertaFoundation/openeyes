<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$blood_reflux_prefill = array_key_exists('blood_reflux', $template_data) && $template_data['blood_reflux'] === '1' ? 'true' : '';
$hpmc_prefill = array_key_exists('hpmc', $template_data) && $template_data['hpmc'] === '1' ? 'true' : '';

?>
<div class="cols-full">
  <table class="cols-full last-left">
    <colgroup>
      <col class="cols-4">
    </colgroup>
    <tbody>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('power_id'); ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'power_id',
                CHtml::listData(
                    OphTrOperationnote_Trabectome_Power::model()->activeOrPk($element->power_id)->findAll(),
                    'id',
                    'name'
                ),
                array('empty' => 'Select', 'nolabel' => true, 'data-prefilled-value' => $template_data['power_id'] ?? ''),
                false,
                array('field' => 3)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('blood_reflux'); ?>
      </td>
      <td>
            <?php
            $prefill_value = array_key_exists('blood_reflux', $template_data) && $template_data['blood_reflux'] === '1' ? 'true' : '';
            echo $form->checkbox(
                $element,
                'blood_reflux',
                array(
                    'class' => 'clearWithEyedraw',
                    'no-label' => true,
                    'data-prefilled-value' => $prefill_value
                )
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('hpmc'); ?>
      </td>
      <td>
            <?php
            $prefill_value = array_key_exists('hpmc', $template_data) && $template_data['hpmc'] === '1' ? 'true' : '';
            echo $form->checkbox(
                $element,
                'hpmc',
                array(
                    'class' => 'clearWithEyedraw',
                    'no-label' => true,
                    'data-prefilled-value' => $prefill_value
                )
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('description'); ?>
      </td>
      <td>
            <?php echo $form->textArea(
                $element,
                'description',
                array('rows' => 4, 'class' => 'autosize clearWithEyedraw', 'nowrapper' => true),
                false,
                array('data-prefilled-value' => $template_data['description'] ?? '')
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
        Complications
      </td>
      <td>

            <?php
            $complications = OphTrOperationnote_Trabectome_Complication::model()->activeOrPk($element->getComplicationIDs())->findAll(array('order' => 'display_order asc'));
            $html_options = array('empty' => '- Complications -', 'nowrapper' => true, 'options' => array());
            foreach ($complications as $comp) {
                $html_options['options'][$comp->id] = array(
                  'data-other' => $comp->other,
                );
            }
            echo $form->multiSelectList(
                $element,
                CHtml::modelName($element) . '[complications]',
                'complications',
                'id',
                CHtml::listData($complications, 'id', 'name'),
                null,
                $html_options,
                false,
                false,
                null,
                false,
                false,
                array('field' => 4)
            )
            ?>
      </td>
    </tr>
    <tr style="<?= $element->hasOtherComplication() ? '' : 'display: none;' ?>"
        id="div_<?= CHtml::modelName($element) ?>_complication_other">
      <td>
        <label for="<?=\CHtml::modelName($element) ?>_complication_other">
            <?php echo $element->getAttributeLabel('complication_other') ?>
        </label>
      </td>
      <td>
            <?php $form->textArea(
                $element,
                'complication_other',
                array('rows' => 2, 'class' => 'autosize', 'nowrapper' => true),
                false,
                array('data-prefilled-value' => $template_data['complication_other'] ?? '')
            ); ?>
      </td>
    </tr>
    </tbody>
  </table>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $(this).delegate('.trabectome .MultiSelectList', 'MultiSelectChanged', function () {
      var container = $(this).closest('.multi-select');
      var selections = container.find('.multi-select-selections');
      var showOther = false;
      selections.find('input').each(function () {
        if ($(this).data('other')) {
          showOther = true;
        }
      });
      if (showOther) {
        $('#div_<?= CHtml::modelName($element)?>_complication_other').show();
      }
      else {
        $('#div_<?= CHtml::modelName($element)?>_complication_other').hide();
        $('#div_<?= CHtml::modelName($element)?>_complication_other').find('textarea').val('');
      }
    });
  });
</script>
