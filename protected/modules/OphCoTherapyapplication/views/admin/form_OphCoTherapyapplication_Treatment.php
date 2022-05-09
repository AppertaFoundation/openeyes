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

?>

<tr>
    <td><?= $model->getAttributeLabel('drug_id'); ?></td>
    <td><?= CHtml::activeDropDownList($model, 'drug_id', CHtml::listData($model->getTreatmentDrugs(), 'id', 'name'), [
            'empty' => 'Select',
            'class' => 'cols-full'
        ]) ?>
    </td>
</tr>
<tr>
    <td><?= $model->getAttributeLabel('decisiontree_id'); ?></td>
    <td><?= CHtml::activeDropDownList($model, 'contraindications_required', [1 => 'Yes', 0 => 'No'], [
            'empty' => 'Select',
            'class' => 'cols-full'
        ]) ?>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('contraindications_required'); ?></td>
    <td>
        <?=\CHtml::activeRadioButtonList($model, 'contraindications_required', [1 => 'Yes', 0 => 'No'], ['separator' => ' ']); ?>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('template_code'); ?></td>
    <td>

        <div class="alert-box info">
            <b>Info</b> The template code is used to determine what form is attached to application email. Leave blank for the default behaviour.
        </div>
        <?=\CHtml::activeTextField($model, 'template_code', [
            'class' => 'cols-full',
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
        ])?></td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('intervention_name'); ?></td>
    <td>
        <?=\CHtml::activeTextField($model, 'intervention_name', [
            'class' => 'cols-full',
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
        ])?></td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('dose_and_frequency'); ?></td>
    <td>
        <?=\CHtml::activeTextArea($model, 'dose_and_frequency', [
            'class' => 'cols-full',
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
        ])?>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('administration_route'); ?></td>
    <td>
        <?=\CHtml::activeTextField($model, 'administration_route', [
            'class' => 'cols-full',
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
        ])?>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('cost_type_id'); ?></td>
    <td>

        <div class="flex-layout cols-12">
            <?=\CHtml::activeTextField($model, 'cost', [
                'class' => 'cols - 4',
                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
            ])?>
            <div class="cols-2">per</div>

            <?=\CHtml::activeDropDownList(
                $model,
                'cost_type_id',
                CHtml::listData(OphCoTherapyapplication_Treatment_CostType::model()->findAll(), 'id', 'name'),
                [
                        'class' => 'cols-4'
                ]
            );?>
        </div>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('monitoring_frequency');?></td>

    <td>
        <div class="flex-layout cols-12">
            <div class="cols-2">Every</div>
            <?=\CHtml::activeTextField($model, 'monitoring_frequency', [
                'class' => 'cols-2',
                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
            ])?>
            <div class="cols-1"></div>
            <?=\CHtml::activeDropDownList(
                $model,
                'monitoring_frequency_period_id',
                CHtml::listData(Period::model()->findAll(), 'id', 'name'),
                [
                    'class' => 'cols - 8'
                ]
            );?>
        </div>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('duration');?></td>
    <td class='cols-full'>
        <?=\CHtml::activeTextArea($model, 'duration', [
            'cols' => 57,
            'class' => 'autosize',
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
        ])?>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('toxicity');?></td>
    <td><?=\CHtml::activeTextArea($model, 'toxicity', [
            'cols' => 57,
            'class' => 'autosize',
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
        ])?>
    </td>
    </tr>
