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

$stay_suture_prefill = array_key_exists('stay_suture', $template_data) && $template_data['stay_suture'] === '1' ? 'true' : '';
$viscoelastic_removed_prefill = array_key_exists('viscoelastic_removed', $template_data) && $template_data['viscoelastic_removed'] === '1' ? 'true' : '';
?>

<div class="cols-full">
  <table class="cols-full last-left">
    <colgroup>
      <col class="cols-6">
    </colgroup>
    <tbody>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('conjunctival_flap_type_id') ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'conjunctival_flap_type_id',
                'OphTrOperationnote_Trabeculectomy_Conjunctival_Flap_Type',
                array(
                    'textAttribute' => 'data-value',
                    'nolabel' => true,
                    'data-prefilled-value' => $template_data['conjunctival_flap_type_id'] ?? ''
                ),
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('stay_suture') ?>
      </td>
      <td>
            <?php
            $prefill_value = array_key_exists('stay_suture', $template_data) && $template_data['stay_suture'] === '1' ? 'true' : '';
            echo $form->checkBox(
                $element,
                'stay_suture',
                array(
                    'text-align' => 'right',
                    'nowrapper' => true,
                    'no-label' => true,
                    'data-prefilled-value' => $prefill_value
                ),
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('site_id') ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'site_id',
                'OphTrOperationnote_Trabeculectomy_Site',
                array('textAttribute' => 'data-value', 'nolabel' => true, 'data-prefilled-value' => $template_data['site_id'] ?? ''),
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('size_id') ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'size_id',
                'OphTrOperationnote_Trabeculectomy_Size',
                array('textAttribute' => 'data-value', 'nolabel' => true, 'data-prefilled-value' => $template_data['size_id'] ?? ''),
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('sclerostomy_type_id') ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'sclerostomy_type_id',
                'OphTrOperationnote_Trabeculectomy_Sclerostomy_Type',
                array('textAttribute' => 'data-value', 'nolabel' => true, 'data-prefilled-value' => $template_data['sclerostomy_type_id'] ?? ''),
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('viscoelastic_type_id') ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'viscoelastic_type_id',
                'OphTrOperationnote_Trabeculectomy_Viscoelastic_Type',
                array('nolabel' => true, 'data-prefilled-value' => $template_data['viscoelastic_type_id'] ?? ''),
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('viscoelastic_removed') ?>
      </td>
      <td>
            <?php
            $prefill_value = array_key_exists('viscoelastic_removed', $template_data) && $template_data['viscoelastic_removed'] === '1' ? 'true' : '';
            echo $form->checkBox(
                $element,
                'viscoelastic_removed',
                array(
                    'text-align' => 'right',
                    'nowrapper' => true,
                    'no-label' => true,
                    'data-prefilled-value' => $prefill_value
                ),
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('viscoelastic_flow_id') ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'viscoelastic_flow_id',
                'OphTrOperationnote_Trabeculectomy_Viscoelastic_Flow',
                array('nowrapper' => true, 'data-prefilled-value' => $template_data['viscoelastic_flow_id'] ?? ''),
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo $form->textArea(
                $element,
                'report',
                array('nowrapper' => true),
                false,
                array('rows' => 6, 'cols' => 40, 'placeholder' => 'Report', 'readonly' => true, 'data-prefilled-value' => $template_data['report'] ?? '')
            ) ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo $form->textArea($element, 'comments', [], false, [ 'rows' => 1, 'data-prefilled-value' => $template_data['comments'] ?? '' ]) ?>
        </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('MultiSelect_Difficulties') ?>
      </td>
      <td>
            <?php
            echo $form->multiSelectList(
                $element,
                'MultiSelect_Difficulties',
                'difficulty_assignments',
                'difficulty_id',
                CHtml::listData(
                    OphTrOperationnote_Trabeculectomy_Difficulty::model()->findAll(array('order' => 'display_order asc')),
                    'id',
                    'name'
                ),
                array(),
                array(
                  'empty' => '- Select -',
                  'label' => 'Operative difficulties',
                  'class' => 'linked-fields',
                  'data-linked-fields' => 'difficulty_other',
                  'data-linked-values' => 'Other',
                  'nowrapper' => true,
                ),
                false,
                false,
                null,
                false,
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr id="<?=  CHtml::modelName($element) ?>_difficulty_other"
        style="<?= !$element->hasMultiSelectValue('difficulties', 'Other') ? "display: none;" : "" ?>">
      <td>
            <?php echo $element->getAttributeLabel('difficulty_other') ?>
      </td>
      <td>
            <?php echo $form->textArea($element, 'difficulty_other', array('nowrapper' => true), false, array('data-prefilled-value' => $template_data['difficulty_other'] ?? '')) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('MultiSelect_Complications') ?>
      </td>
      <td>
            <?php echo $form->multiSelectList(
                $element,
                'MultiSelect_Complications',
                'complication_assignments',
                'complication_id',
                CHtml::listData(
                    OphTrOperationnote_Trabeculectomy_Complication::model()->findAll(array('order' => 'display_order asc')),
                    'id',
                    'name'
                ),
                array(),
                array(
                  'empty' => '- Select -',
                  'label' => 'Complications',
                  'class' => 'linked-fields',
                  'data-linked-fields' => 'complication_other',
                  'data-linked-values' => 'Other',
                  'nowrapper' => true,
                ),
                false,
                false,
                null,
                false,
                false,
                array('field' => 4)
            ) ?>
      </td>
    </tr>
    <tr id="<?= CHtml::modelName($element) ?>_complication_other"
        style="<?= $element->hasMultiSelectValue('complications', 'Other') ? '' : 'display: none;' ?>">
      <td>
            <?php echo $element->getAttributeLabel('complication_other') ?>
      </td>
      <td>
            <?php echo $form->textArea($element, 'complication_other', array('nowrapper' => true), false, array('data-prefilled-value' => $template_data['complication_other'] ?? '')) ?>
      </td>
    </tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(this).delegate('.trabeculectomy .MultiSelectList', 'MultiSelectChanged', function () {
            if ($.trim($(this).attr('id')) === 'MultiSelect_Difficulties'){
                toggleAdditionalFields($(this), '_difficulty_other');

            } else if ($.trim($(this).attr('id')) ==='MultiSelect_Complications'){
                toggleAdditionalFields($(this), '_complication_other');
            }
        });

        function toggleAdditionalFields (element , field_name){
            var container = element.closest('.multi-select');
            var selections = container.find('.multi-select-selections');
            var showOther = false;
            var listItems = selections.find('li');
            listItems.find('span').each(function(){
                if ($.trim($(this).text()) == 'Other'){
                    showOther = true;
                }
            })
            if (showOther) {
                $('#<?= CHtml::modelName($element)?>'+field_name).show();
            }
            else {
                $('#<?=\CHtml::modelName($element)?>'+field_name).hide();
                $('#<?= CHtml::modelName($element)?>'+field_name).find('textarea').val('');
            }
        }


    });
</script>
