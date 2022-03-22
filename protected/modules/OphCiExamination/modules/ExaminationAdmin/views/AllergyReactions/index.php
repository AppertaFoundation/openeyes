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
    <h2>Edit Allergy Reactions</h2>
</div>
<?php $this->renderPartial('//base/_messages') ?>
<div class="cols-6">
    <form id="admin_Allergy_Reactions" method="post" action="/OphCiExamination/admin/AllergyReactions/update">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="page" value="1">
        <table class="standard generic-admin sortable">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr data-id="<?= $model->id ?>" >
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <?= CHtml::hiddenField("OphCiExamination_AllergyReaction[{$i}][id]", $model->id); ?>
                    </td>
                    <td>
                        <?= CHtml::textField("OphCiExamination_AllergyReaction[{$i}][name]", $model->name); ?>
                    </td>
                    <td>
                        <?= CHtml::checkBox("OphCiExamination_AllergyReaction[{$i}][active]", $model->active); ?>
                    </td>
                    <td>
                        <?php if (!$model->isInUse()) {
                            echo CHtml::link(
                                'Delete',
                                "delete?id={$model->id}",
                                [
                                    'class' => 'button large',
                                    'type' => 'button',
                                    'name' => "delete[{$i}]",
                                    'id' => "et_delete_[{$i}]"
                                ]
                            );
                        } else {
                            echo '<i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="Allergy reaction cannot be removed because it is in use. Please set as inactive instead."></i>';
                        }?>
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
<script id="allergy_reaction_row" type="x-tmpl-mustache">
<tr>
    <td class="reorder">
        <span>&uarr;&darr;</span>
    </td>
    <td>
        <?= CHtml::textField("OphCiExamination_AllergyReaction[{{ new }}][name]"); ?>
    </td>
    <td>
        <?= CHtml::checkBox("OphCiExamination_AllergyReaction[{{ new }}][active]"); ?>
    </td>
    <td>
        <?= \CHtml::button(
            'Delete',
            [
                'class' => 'button large',
                'type' => 'button',
                'name' => 'delete[{{new}}]',
                'id' => 'et_delete_{{new}}'
            ]
        ); ?>
    </td>
</tr>
</script>
<script>
    $(document).ready(function(){
        $('#add_new_row').click(function(){
            let $table = $('#admin_Allergy_Reactions table tbody');
            let template = $('#allergy_reaction_row').html();
            Mustache.parse(template);
            let lastRowIndex = $table.find('tr').length;
            let $rendered = Mustache.render(template, {new: lastRowIndex});
            $table.append($rendered);
            let deleteButtonSelectorString = `#et_delete_${lastRowIndex}`;
            $(deleteButtonSelectorString).click(function(){
                $(deleteButtonSelectorString).parent().parent().remove();
            });
        });
    });
    $('.generic-admin.sortable tbody').sortable();
</script>
