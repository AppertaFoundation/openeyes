<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php if (\Yii::app()->user->hasFlash('error')) : ?>
    <div class='alert-box error'>
        <?= \Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>

<div class="row divider">
    <h2>Drug Sets Admin</h2>
</div>

<div class="row divider">
    <h2>Set type:</h2>
    <?=$this->renderPartial('/AutoSetRule/_index_filter', ['search' => $search]);?>
</div>

<div class="cols-12">
    <form id="admin_DrugSets">
        <table id="drugset-list" class="standard last-right">
            <colgroup>
                <col style="width:3.33333%;">
                <col style="width:3.33333%">
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">

            </colgroup>
            <thead>
            <tr>
                <th><?= \CHtml::checkBox('selectall'); ?></th>
                <th>Id</th>
                <th>Name</th>
                <th>Rule</th>
                <th>Item Count</th>
                <th>Hidden</th>
                <th style="text-align:unset;width:12.1%">Actions</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data_provider->getData() as $set) {
                    $this->renderPartial('/AutoSetRule/_row', ['set' => $set]);
                }
                ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="4">
                    <?= \CHtml::submitButton('Add', [
                        'id' => 'et_add',
                        'data-uri' => "/OphDrPrescription/admin/AutoSetRule/edit",
                        'class' => 'button large'
                    ]); ?>
                    <?= \CHtml::button('Delete', [
                        'id' => 'delete_sets',
                        'class' => 'button large',
                    ]); ?>
                    <?=\CHtml::linkButton($button_name,
                        array('href' => '/OphDrPrescription/admin/AutoSetRule/populateAll',
                            'class' => 'button large ' . $button_status)); ?>

                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $data_provider->pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/html" id="medication_set_template" style="display:none">
    <tr>
        <td><input type="checkbox" value="{{id}}" name="delete-ids[]" /></td>
        <td>{{id}}</td>
        <td>{{name}}</td>
        <td>{{rules}}</td>
        <td>{{count}}</td>
        <td>
            {{#hidden}}<i class="oe-i tick medium"></i>{{/hidden}}
            {{^hidden}}<i class="oe-i remove medium"></i>{{/hidden}}
        </td>
        <td>
            <a href="/OphDrPrescription/admin/autoSetRule/edit/{{id}}" class="button">Edit</a>
            <a href="/OphDrPrescription/admin/autoSetRule/listMedications?set_id={{id}}" class="button">List medications</a>
        </td>

    </tr>
</script>

<script>
    let drugSetController = new OpenEyes.OphDrPrescriptionAdmin.DrugSetController({
        searchUrl: '/OphDrPrescription/admin/autoSetRule/search',
        deleteUrl: '/OphDrPrescription/admin/autoSetRule/delete'
    });

    let $rebuild_button = $('#yt2');

    (function checkCommand() {
        if ($rebuild_button.hasClass('disabled')) {
            $.ajax({
                url: '/OphDrPrescription/admin/AutoSetRule/CheckRebuildIsRunning',
                dataType: "text",
                success: function (is_running) {
                    if (is_running) {
                        setTimeout(checkCommand, 5000);
                    } else {
                        $rebuild_button.removeClass('disabled');
                        $rebuild_button.html('Rebuild all sets now');
                    }
                },
                error: function (error) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Something went wrong while rebuilding the sets, please try later."
                    }).open();
                }
            });
        }
    })();
</script>
