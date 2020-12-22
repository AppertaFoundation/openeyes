<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">

    <div class="row divider">
        <h2>Edit contact label</h2>
    </div>

    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'editContactLabelForm',
            'enableAjaxValidation' => false,
            'focus' => '#ContactLabel_name',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ]
    ) ?>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td>
                <?= \CHtml::activeTextField(
                    $contactlabel,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Is Private</td>
            <td><?= \CHtml::activeCheckBox(
                $contactlabel,
                'is_private',
                []
            );
?>
            </td>
        </tr>
        <tr>
            <td>Max Number Per Patient</td>
            <td>
                <?= \CHtml::activeNumberField(
                    $contactlabel,
                    'max_number_per_patient'
                ); ?>
            </td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="5">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?= \CHtml::submitButton(
                    'Cancel',
                    [
                        'class' => 'button large',
                        'data-uri' => '/admin/contactlabels',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
                <?= \CHtml::button(
                    'Delete',
                    [
                        'class' => 'button large',
                        'name' => 'delete',
                        'id' => 'et_delete'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>
<script type="text/javascript">
    handleButton($('#et_delete'), function (e) {
        e.preventDefault();

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/admin/deleteContactLabel',
            'data': 'contact_label_id=<?php echo $contactlabel->id?>&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
            'success': function (response) {
                if (response == 0) {
                    window.location.href = baseUrl + '/admin/contactLabels';
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "You cannot delete this contact label because it's in use by " + response + " contacts."
                    }).open();
                    enableButtons();
                }
            }
        });
    });

    function sort_selectbox(element) {
        rootItem = element.children('option:first').text();
        element.append(element.children('option').sort(selectSort));
    }

    function selectSort(a, b) {
        if (a.innerHTML == rootItem) {
            return -1;
        } else if (b.innerHTML == rootItem) {
            return 1;
        }
        return (a.innerHTML > b.innerHTML) ? 1 : -1;
    };

    var rootItem = null;
</script>
