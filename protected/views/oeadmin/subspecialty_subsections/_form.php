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

<?=\CHtml::errorSummary(
    $model,
    null,
    null,
    ["class" => "alert-box alert with-icon"]
); ?>

<div class="cols-full">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-full">
        </colgroup>
        <tbody>
            <tr>
                <td>Name</td>
                <td class="cols-full">
                <?=\CHtml::activeTextField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
                <?=\CHtml::activeHiddenField(
                    $model,
                    'subspecialty_id',
                    [ 'value' => $subspecialty_id ]
                ); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <?= \OEHtml::submitButton() ?>
    
    <?php if ($model->id) {
        echo \OEHtml::Button("Delete", [
            'id' => 'et_delete_subspecialty_subsection',
            'data-id' => $model->id,
            'data-subspecialty_id' => $subspecialty_id
        ]);
    } ?>

    <?= \OEHtml::cancelButton("Cancel", [
        'data-uri' => '/oeadmin/subspecialtySubsections/list?subspecialty_id=' . $subspecialty_id,
    ]) ?>
</div>
<script>
    $('#et_delete_subspecialty_subsection').click( event => {
        let alert = new OpenEyes.UI.Dialog.Confirm({
            title: 'Delete Subsection',
            content: 'Are you sure you want to delete this subsection?'
        });
        alert.content.on('click', '.ok', (sub_event, main_event=event) => {
            let params = main_event.target.dataset;
            $.ajax({
                'type': 'POST',
                'url': baseUrl + '/oeadmin/subspecialtySubsections/delete?id=' + params['id'] + '&subspecialty_id=' + params['subspecialty_id'],
                'data': "YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                'success': function (response) {
                    if (response === 'false') {
                        let delete_alert = new OpenEyes.UI.Dialog.Alert({
                            content: "Please delete the procedures for this subsection before removing this subsection."
                        });

                        delete_alert.content.on('click', '.ok', (sub_event, main_event=event) => {
                            window.location.href = '/oeadmin/subspecialtySubsections/list?subspecialty_id=' + params['subspecialty_id'];
                        });

                        delete_alert.open();
                    } else {
                        window.location.href = '/oeadmin/subspecialtySubsections/list?subspecialty_id=' + params['subspecialty_id'];
                    }
                }
            });
        });

        event.preventDefault();
        alert.open();
    });
</script>
