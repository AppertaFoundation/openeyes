<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/CataractSurgicalManagement.js", CClientScript::POS_HEAD);
$primary_reasons = OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery::model()->findAll('active = 1');
?>
<div class="element-fields element-eyes">
    <?= \CHtml::activeHiddenField($element, 'eye_id', [ 'class' => 'sideField' ]); ?>
    <?= $this->renderPartial(
        'form_Element_OphCiExamination_CataractSurgicalManagement_Side',
        [
        'element' => $element,
        'side' => 'right',
        'primary_reasons' => $primary_reasons,
        ]
    ) ?>
    <?= $this->renderPartial(
        'form_Element_OphCiExamination_CataractSurgicalManagement_Side',
        [
        'element' => $element,
        'side' => 'left',
        'primary_reasons' => $primary_reasons,
        ]
    ) ?>
</div>


<script>
let cataractSurgicalManagementController;
$(document).ready(function () {
  cataractSurgicalManagementController = new OpenEyes.OphCiExamination.CataractSurgicalManagementController({
    element: $('.<?=CHtml::modelName($element)?>'),
    <?php foreach (['left', 'right'] as $side) : ?>
        <?= $side ?>PrimaryReasons:
        <?=CJSON::encode(
            array_map(function ($reason) use ($element, $side) {
                return [
                'label' => $reason->name,
                'id' => $reason->id,
                'type' => 'primary_reason',
                'selected' => $reason->id === $element->{$side . '_reason_for_surgery_id'} ? 'selected' : '',
                ];
            }, $primary_reasons)
        )?>,

        <?= $side ?>Discussed: [
          {
            'label': 'Yes, discussed with Patient',
            'id': 1,
            'type': 'discussed',
            'conditional-id': '<?=$side?>-discussed-1',
            'selected': '<?= $element->{$side . '_correction_discussed'} === '1' ? 'selected' : ''?>'
          },
          {
            'label': 'No, not discussed with Patient',
            'id': 0,
            'type': 'discussed',
            'conditional-id': '<?=$side?>-discussed-0',
            'selected': '<?= $element->{$side . '_correction_discussed'} === '0' ? 'selected' : ''?>'
          }
        ],
        <?= $side ?>GuardedPrognosis: [
          {
            'label': 'Yes',
            'id': 1,
            'type': 'guarded_prognosis',
            'selected': '<?= (string)$element->{$side . '_guarded_prognosis'} === '1' ? 'selected' : ''?>'
          },
          {
            'label': 'No',
            'id': 0,
            'type': 'guarded_prognosis',
            'selected': '<?= (string)$element->{$side . '_guarded_prognosis'} === '0' ? 'selected' : ''?>'
          }
        ],
        <?= $side ?>RefractiveCategories: [
          {
            'label': 'Emmetropia',
            'id': 0,
            'conditional-id': '<?=$side?>-refractive-category-0',
            'type': 'refractive_category',
          },
          {
            'label': 'Myopia',
            'id': 1,
            'conditional-id': '<?=$side?>-refractive-category-1',
            'type': 'refractive_category',
          },
          {
            'label': 'Other',
            'id': 2,
            'conditional-id': '<?=$side?>-refractive-category-2',
            'type': 'refractive_category',
          },
        ],
        <?= $side ?>RefractiveEmmetropia: [
          {
            'label': '+0.00',
            'value': '+0.00',
            'type': 'refractive_emmetropia',
            'selected': 'selected',
          },
        ],
        <?= $side ?>RefractiveMyopia:
        <?=CJSON::encode(
            array_map(function ($value) {
                return [
                'label' => $value,
                'value' => $value,
                'type' => 'refractive_myopia',
                ];
            }, ['-0.50','-0.75','-1.00','-1.50','-2.00','-2.50', '-3.00'])
        )?>,
        <?=$side?>RefractiveCategoriesOptions: {
        'id': '<?=$side?>_refractive_categories_0',
        'header': 'Refractive target',
        'hideByDefault': true,
        'conditionalFlowMaps': {
          '<?=$side?>-refractive-category-0': [{'target-group': '<?=$side?>_refractive_group', 'target-id': 'emmetropia' }],
          '<?=$side?>-refractive-category-1': [{'target-group': '<?=$side?>_refractive_group', 'target-id': 'myopia' }],
          '<?=$side?>-refractive-category-2': [{'target-group': '<?=$side?>_refractive_group', 'target-id': 'other' }],
        },
      },
        <?=$side?>RefractiveEmmetropiaOptions: {
        'id': '<?=$side?>_refractive_group_emmetropia',
        'hideByDefault': true,
      },
        <?=$side?>RefractiveMyopiaOptions: {
        'id': '<?=$side?>_refractive_group_myopia',
        'hideByDefault': true,
      },
        <?=$side?>RefractiveTargetOptions: {
        'id': '<?=$side?>_refractive_group_other',
        'hideByDefault': true,
        'supportSigns': true,
        'splitIntegerNumberColumns': [{'min':0, 'max':1}, {'min':0, 'max':9}],
        'splitIntegerNumberColumnsTypes': ['first_digit', 'second_digit'],
        'decimalValues': ['.00', '.25', '.50', '.75'],
        'decimalValuesType': 'decimal',
        'supportDecimalValues': true,
      },
        <?=$side?>DiscussedOptions: {
      'id': '<?=$side?>_discussed',
      'header': 'Refractive target discussed with patient',
      'mandatory': true,
      'deselectOnReturn':'false',
      'conditionalFlowMaps': {
        '<?=$side?>-discussed-1': [{'target-group': '<?=$side?>_refractive_categories', 'target-id': '0' }],
        '<?=$side?>-discussed-0': [
          {'target-group': '<?=$side?>_refractive_categories', 'target-id': '' },
          {'target-group': '<?=$side?>_refractive_group', 'target-id': '' },
        ],
      },
    },
    <?php endforeach; ?>
      primaryReasonsOptions: {'id': 'primary_reason', 'header': 'Primary reason', 'mandatory':'true', 'deselectOnReturn':'false'},
      guardedPrognosisOptions: {'id': 'guarded_prognosis', 'header': 'Guarded Prognosis', 'mandatory': 'true', 'deselectOnReturn':'false'},
  });
});
</script>
