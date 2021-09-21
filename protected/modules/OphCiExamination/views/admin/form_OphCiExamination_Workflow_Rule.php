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

<table class="standard cols-full">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>
    <colgroup>
        <col class="cols-3">
        <col class="cols-5">
    </colgroup>
    <tbody>
    <?php if ($form->errorSummary($model)) : ?>
        <tr>
            <td>Errors</td>
            <td class="cols-full">
                <?php echo $form->errorSummary($model) ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td>Context</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'firm_id',
                CHtml::listData(Firm::model()->activeOrPk($model->firm_id)->findAllByAttributes(array('institution_id' => Institution::model()->getCurrent()->id)), 'id', 'nameAndSubspecialty'),
                ['class' => 'cols-full', 'empty' => '- All -']
            ); ?>
        </td>
    </tr>
    <tr>
        <td>Episode status</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'episode_status_id',
                CHtml::listData(EpisodeStatus::model()->findAll(), 'id', 'name'),
                ['class' => 'cols-full', 'empty' => '- All -']
            ); ?>
        </td>
    </tr>
    <tr>
        <td>Subspecialty</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'subspecialty_id',
                CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
                ['class' => 'cols-full', 'empty' => '- All -']
            ); ?>
        </td>
    </tr>
    <tr>
        <td>Workflow</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'workflow_id',
                CHtml::listData(
                    OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->findAllByAttributes(array('institution_id' => Institution::model()->getCurrent()->id)),
                    'id',
                    'name'
                ),
                ['class' => 'cols-full']
            ); ?>
        </td>
    </tr>
    </tbody>
</table>