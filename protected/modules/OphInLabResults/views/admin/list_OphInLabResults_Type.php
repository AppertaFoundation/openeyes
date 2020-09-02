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

<?php $this->renderPartial('//base/_messages') ?>

<div class="cols-12">
    <div class="alert-box error with-icon js-admin-errors" style="display:none">
        <p>Could not be deleted:</p>
        <div class="js-admin-error-container"></div>
    </div>
    <div class="row divider">

    </div>
    <form id="admin_list_disorders">
        <input type="hidden" name="page" value="1">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard generic-admin cols-full sortable" id="et_sort" data-uri = "/OphInLabResults/oeadmin/resultType/sortTypes">
            <colgroup>
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
            </colgroup>
            <thead>
            <tr>
                <th>Order</th>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Type</th>
                <th>Field Type</th>
                <th>Show Units</th>
                <th>Units Editable</th>
                <th>Default Units</th>
                <th>Custom Warning Message</th>
                <th>Show on Whiteboard</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphInLabResults/oeadmin/resultType/edit/<?php echo $model->id ?>">
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <?= CHtml::hiddenField($model_class."[display_order][]", $model->id); ?>
                    </td>
                    <td><input type="checkbox" name="resultTypes[]" value="<?php echo $model->id ?>"/></td>
                    <td><?= $model->type; ?></td>
                    <td><?= $model->fieldType->name ?></td>
                    <td><?= $model->show_units ?></td>
                    <td><?= $model->allow_unit_change ?></td>
                    <td><?= $model->default_units ? $model->default_units : '-' ?></td>
                    <td><?= $model->custom_warning_message ? $model->custom_warning_message : '-' ?></td>
                    <td><?= $model->show_on_whiteboard ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tr>
                <td colspan="2">
                    <?= CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?= CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_result_type',
                            'id' => 'et_delete_result_type'
                        ]
                    ); ?>
                </td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#et_delete_result_type').click(function (e) {
            e.preventDefault();

            let $checked = $('input[name="resultTypes[]"]:checked');
            if ($checked.length === 0) {
                alert('Please select one or more result type data to delete.');
                return;
            }

            $.ajax({
                'type': 'POST',
                'url': baseUrl + '/OphInLabResults/oeadmin/resultType/delete',
                'data': $checked.serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                'success': function (response) {
                    response = JSON.parse(response);
                    if (response['status'] === 1) {
                        window.location.reload();
                    } else {
                        $('.js-admin-errors').show();
                        let $errorContainer = $('.js-admin-error-container');
                        $errorContainer.html("");

                        response['errors'].forEach(function (error) {
                            $errorContainer.append('<p class="js-admin-errors">' + error + '</p>');
                        });
                    }
                }
            });
        });

        $('.generic-admin.sortable tbody').sortable({
            stop: OpenEyes.Admin.saveSorted
        });
    });
</script>