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
    <form id="admin_data_sources">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkall" class="sources"/></th>
                <th>Name</th>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach (ImportSource::model()->findAll(array('order' => 'name')) as $i => $source) { ?>
                <tr class="clickable" data-id="<?php echo $source->id ?>"
                    data-uri="admin/editdatasource/<?php echo $source->id ?>">
                    <td><input type="checkbox" name="source[]" value="<?php echo $source->id ?>" class="sources"/></td>
                    <td><?php echo $source->name ?>&nbsp;</td>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot>
            <td colspan="2">
                <?= \CHtml::button(
                    'Add',
                    [
                        'class' => 'button large',
                        'name' => 'add',
                        'id' => 'et_add'
                    ]
                ); ?>
                <?= \CHtml::button(
                    'Delete',
                    [
                        'class' => 'button large',
                        'name' => 'delete',
                        'data-uri' => '/admin/deleteDataSources',
                        'data-object' => 'data_sources',
                        'id' => 'et_delete'
                    ]
                ); ?>
            </td>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/javascript">
    $('#et_delete').click(function (e) {
        e.preventDefault();

        if ($('input[type="checkbox"][name="source[]"]:checked').length == 0) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select the source(s) you wish to delete."
            }).open();
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/admin/deleteDataSources',
            'data': $('#admin_data_sources').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (resp) {
                if (resp == "1") {
                    window.location.reload();
                } else {
                    if ($('input[type="checkbox"][name="source[]"]:checked').length == 1) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: "The source you selected is in use and cannot be deleted."
                        }).open();
                    } else {
                        new OpenEyes.UI.Dialog.Alert({
                            content: "The sources you selected are in use and cannot be deleted."
                        }).open();
                    }
                }
            }
        });
    });
</script>
