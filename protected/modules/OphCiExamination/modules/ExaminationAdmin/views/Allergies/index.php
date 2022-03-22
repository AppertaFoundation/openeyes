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
    <form id="admin_Allergies" method="post" action="/OphCiExamination/admin/Allergies/update">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="page" value="1">
        <table class="standard generic-admin sortable">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Allergic to Medication Set</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr data-id="<?= $model->id ?>" >
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <?= CHtml::hiddenField("OphCiExamination_Allergy[{$i}][id]", $model->id); ?>
                    </td>
                    <td>
                        <?= CHtml::textField("OphCiExamination_Allergy[{$i}][name]", $model->name); ?>
                    </td>
                    <td>
                        <?= CHtml::dropDownList("OphCiExamination_Allergy[{$i}][medication_set_id]", $model->medication_set_id, CHtml::listData($medication_set_list_options, 'id', 'name'), ["empty" => "- Please Select -"]); ?>
                    </td>
                    <td>
                        <?= CHtml::checkBox("OphCiExamination_Allergy[{$i}][active]", $model->active); ?>
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
                                'id' => 'add_new_row'
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
<script id="allergy_row" type="x-tmpl-mustache">
<tr>
    <td class="reorder">
        <span>&uarr;&darr;</span>
    </td>
    <td>
        <?= CHtml::textField("OphCiExamination_Allergy[{{ new }}][name]"); ?>
    </td>
    <td>
        <?= CHtml::dropDownList("OphCiExamination_Allergy[{{ new }}][medication_set_id]", '0', CHtml::listData($medication_set_list_options, 'id', 'name'), ["empty" => "- Please Select -"]); ?>
    </td>
    <td>
        <?= CHtml::checkBox("OphCiExamination_Allergy[{{ new }}][active]"); ?>
    </td>
</tr>
</script>
<script>
    $(document).ready(function(){
        $('#add_new_row').click(function(){
            let $table = $('#admin_Allergies table tbody');
            let template = $('#allergy_row').html();
            Mustache.parse(template);
            let $rendered = Mustache.render(template, {new: $table.find('tr').length});
            $table.append($rendered);
        });
    });
    $('.generic-admin.sortable tbody').sortable();
</script>
