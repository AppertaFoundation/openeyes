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
<div class="row divider cols-5">
    <h2>Edit Allergies</h2>
</div>
<?php $this->renderPartial('//base/_messages') ?>
<div class="cols-6">
    <form id="admin_Drugs">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="page" value="1">
        <table class="standard generic-admin sortable" id="et_sort" data-uri = "/OphCiExamination/admin/Allergies/sortAllergies">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Medication Set Id</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr data-id="<?= $model->id ?>" >
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <?= CHtml::hiddenField("OphCiExamination_Allergy[display_order][]", $model->id); ?>
                    </td>
                    <td>
                        <?= CHtml::textField("name[{$i}]", $model->name); ?>
                    </td>
                    <td>
                        <?= CHtml::dropDownList("medication_set_id[{$i}]", $model->medication_set_id, ['0' => '- Please Select -']+CHtml::listData($medication_set_list_options, 'id', 'name')); ?>
                    </td>
                    <td>
                        <?= CHtml::checkBox("active[{$i}]", $model->active); ?>
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
                                'data-uri' => '/OphCiExamination/admin/Drug/create',
                                'id' => 'et_add'
                            ]
                        ); ?>
                        <?= \CHtml::button(
                            'Save',
                            [
                                'class' => 'button large',
                                'type' => 'submit',
                                'name' => 'save',
                                'id' => 'et_save'
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
