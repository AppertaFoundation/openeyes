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
<h2>Follow-up Roles</h2>
<?php $this->renderPartial('//base/_messages') ?>
<div class="cols-full">
    <form id="admin_clinicoutcomeroles">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="page" value="1">
        <table class="standard generic-admin sortable" id="et_sort" data-uri = "/OphCiExamination/admin/ClinicOutcomeRoles/sortClinicOutcomeRoles">
            <thead>
                <tr>
                    <th><input type="checkbox" name="selectall" id="selectall"/></th>
                    <th>Display order</th>
                    <th>Name</th>
                    <th>Institution</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?= $model->id ?>"
                    data-uri="OphCiExamination/admin/ClinicOutcomeRoles/update/<?= $model->id ?>" >
                    <td><input type="checkbox" name="select[]" value="<?= $model->id ?>"/></td>
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <input type="hidden" name="OphCiExamination_ClinicOutcome_Role[display_order][]" value="<?= $model->id ?>">
                    </td>
                    <td>
                        <?= $model->name ?>
                    </td>
                    <td>
                        <?= $model->institution ? $model->institution->name : 'N/A' ?>
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
                                'data-uri' => '/OphCiExamination/admin/ClinicOutcomeRoles/create',
                                'id' => 'et_add'
                            ]
                        ); ?>

                        <?= \CHtml::button(
                            'Delete',
                            [
                                'class' => 'button large',
                                'name' => 'delete',
                                'data-object' => 'clinicoutcomeroles',
                                'data-uri' => '/OphCiExamination/admin/ClinicOutcomeRoles/delete',
                                'id' => 'et_delete'
                            ]
                        ); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<script>
    $('.generic-admin.sortable tbody').sortable({
        stop: OpenEyes.Admin.saveSorted
    });
</script>
