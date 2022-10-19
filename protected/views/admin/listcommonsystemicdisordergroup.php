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
<h2>Common Systemic Disorder Groups</h2>
<?php
$this->renderPartial('//base/_messages');
if (isset($errors)) {
    $this->renderPartial('/admin/_form_errors', $errors);
}
?>

<div class="alert-box error with-icon js-admin-errors" style="display:none">
    <p>Could not be deleted:</p>
    <div class="js-admin-error-container"></div>
</div>

<form method="get">
    <table class="cols-7">
        <colgroup>
            <col class="cols-3">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="col-gap">            
            <td>&nbsp;<br/><?=\CHtml::dropDownList(
                    'institution_id',
                    $current_institution_id,
                    Institution::model()->getTenantedList(!Yii::app()->user->checkAccess('admin'))
                ) ?></td>
        </tr>
        </tbody>
    </table>
</form>

<div class="cols-5">
    <form id="admin_commonsystemicdisordergroup" method="POST" action="/oeadmin/CommonSystemicDisorderGroup/save?institution_id=<?= $current_institution_id; ?>">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="page" value="1">
        <table class="standard entry-table sortable">
        <?php if (empty($model_list)) { ?>
            <tbody>
            <tr><td><span class="empty">No results found.</span></td></tr>
            </tbody>
        <?php } else { ?>
            <colgroup>
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-5">
            </colgroup>
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Order</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody class="sortable">
            <?php foreach ($model_list as $key => $model) { ?>
                <tr class="clickable" data-id="<?= $model->id ?>"
                    data-uri="/oeadmin/CommonSystemicDisorderGroup/update/<?= $model->id ?>" >
                    <td>
                        <input type="checkbox" name="select[]" value="<?= $model->id ?>"/>
                    </td>
                    <td class="reorder">
                        <input type="hidden" name="CommonSystemicDisorderGroup[<?= $key ?>][id]" value="<?= $model->id ?>"/>
                        <input type="hidden" name="CommonSystemicDisorderGroup[<?= $key ?>][display_order]" value="<?= $model->display_order ?>"/>
                        <span>&uarr;&darr;</span>
                    </td>
                    <td>
                        <?= $model->name ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        <?php } ?>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => '/oeadmin/CommonSystemicDisorderGroup/create',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?php
                    if (count($model_list) > 1) {
                        echo \CHtml::button(
                            'Save',
                            [
                            'class' => 'button large',
                            'type' => 'submit',
                            'name' => 'save',
                            ]
                        );
                    } ?>
                    <?= \CHtml::button(
                        'Delete',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'delete',
                            'data-uri' => '/oeadmin/CommonSystemicDisorderGroup/delete',
                            'id' => 'et_delete_disorder_group',
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('.entry-table').on('click', 'tr.clickable', function (e) {
            e.preventDefault();
            let uri = $(this).data('uri');

            if (uri) {
                window.location.href = uri;
            }
        });

        $(':checkbox').on('click', function (e) {
            e.stopPropagation();
        });

        $('.sortable tbody').sortable({
            stop: function() {
                $('.sortable tbody tr').each(function(index, tr) {
                    $(tr).find("[name$='display_order]']").val(index);
                });
            }
        });

        $('#institution_id').on('change', function () {
            $(this).closest('form').submit();
        });

        $('#et_delete_disorder_group').on('click', function () {
            let $checked = $('input[name="select[]"]:checked');
            if ($checked.length === 0) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Please select one or more common systemic disorder group to delete."
                }).open();
            } else {
                $.ajax({
                    'type': 'POST',
                    'url': baseUrl + $(this).data('uri'),
                    'data': $checked.serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                    'dataType': 'JSON',
                    'success': function (response) {
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
            }
        });
    });
</script>