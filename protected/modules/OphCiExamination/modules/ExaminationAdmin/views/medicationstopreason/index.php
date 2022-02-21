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
<h2>Medication Stop Reason</h2>
<?php $this->renderPartial('//base/_messages') ?>

<div class="alert-box error with-icon js-admin-errors" style="display:none">
    <p>Could not be deleted:</p>
    <div class="js-admin-error-container"></div>
</div>

<div class="cols-full">
    <form id="admin_medication_stop_reason">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="page" value="1">
        <table class="standard generic-admin">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Name</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                            <?php $editable = !in_array($model->name, $this->reasons_that_cannot_be_edited) ?>
                            <tr class="<?= $editable ? 'clickable' : ''  ?>" data-id="<?= $model->id ?>"
                    data-uri="OphCiExamination/admin/MedicationStopReason/update/<?= $model->id ?>" >
                    <td><input type="checkbox" name="select[]" value="<?= $model->id ?>"/></td>
                    <td class="stop-reason-name">
                                                <?php if (!$editable) { ?>
                                                    <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="This element cannot be edited."></i>
                                                <?php } ?>
                        <?= $model->name ?>
                    </td>
                    <td>
                        <?= ($model->active) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>'); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => '/OphCiExamination/admin/MedicationStopReason/create',
                            'id' => 'et_add'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
