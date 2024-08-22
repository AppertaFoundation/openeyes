<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$default_states = [
    PathwayStep::STEP_REQUESTED => 'To Do',
    PathwayStep::STEP_STARTED => 'Active',
    PathwayStep::STEP_COMPLETED => 'Completed',
    PathwayStep::STEP_DRAFT => 'Draft',
]
?>
<form id="admin_pathsteps" method="post">
    <div class="admin box">
        <?php if ($custom_pathsteps) { ?>
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
            <table class="standard">
                <thead>
                <tr>
                    <th><input type="checkbox" name="selectall" id="selectall"/></th>
                    <th>Long Name</th>
                    <th>Short Name</th>
                    <th>Default State</th>
                    <th>Active</th>
                    <th>Assigned to current institution</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($custom_pathsteps as $pathstep) { ?>
                <tr class="clickable" data-id="<?= $pathstep->id ?>"
                    data-uri="Admin/worklist/editCustomPathStep/<?= $pathstep->id ?>">
                    <td><input type="checkbox" name="select[]" value="<?= $pathstep->id ?>"
                               id="select[<?= $pathstep->id ?>]"/></td>
                    <td><?= $pathstep->long_name ?></td>
                    <td><?= $pathstep->short_name ?></td>
                    <td><?= $pathstep->default_state !== null ? $default_states[$pathstep->default_state] : 'N/A' ?></td>
                    <td><i class="oe-i small <?= $pathstep->active ? 'tick' : 'remove' ?>"></i></td>
                    <td>
                        <?= ($pathstep->hasMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id'])) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>')?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert-box info">No custom path steps have been defined</div>
        <?php } ?>
        <?= CHtml::link('Add Custom Path Step', '/Admin/worklist/editCustomPathStep', [
            'class' => 'button large',
        ]) ?>
        <?=\CHtml::submitButton(
            'Add Selected to Current Institution',
            [
                'class' => 'button large',
                'formaction' => '/Admin/worklist/addStepInstitutionMapping',
                'name' => 'addmapping',
                'id' => 'et_add_mapping'
            ]
        ) ?>
        <?=\CHtml::submitButton(
            'Remove Selected from Current Institution',
            [
                'class' => 'button large',
                'formaction' => '/Admin/worklist/deleteStepInstitutionMapping',
                'name' => 'deletemapping',
                'id' => 'et_delete_mapping'
            ]
        ) ?>
    </div>
</form>