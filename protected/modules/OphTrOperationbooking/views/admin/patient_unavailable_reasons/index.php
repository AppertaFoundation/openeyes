<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">
    <form id="admin_patientunavailablereasons">
        <table class="standard">
            <thead>
            <tr>
                <th>Enabled</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody class="sortable" data-sort-uri="/OphTrOperationbooking/admin/sortpatientunavailablereasons">
            <?php
            $criteria = new CDbCriteria();
            $criteria->order = 'display_order asc';
            foreach (OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll() as $i => $patientunavailablereason) {?>
                <tr class="clickable" data-attr-id="<?php echo $patientunavailablereason->id?>" data-uri="OphTrOperationbooking/admin/editpatientunavailablereason/<?php echo $patientunavailablereason->id?>">
                    <td><input type="checkbox" name="patientunavailablereason[]" value="<?php echo $patientunavailablereason->id?>" class="patientunavailablereasons-enabled" <?php if ($patientunavailablereason->enabled) { echo 'checked'; } ?> /></td>
                    <td><?php echo $patientunavailablereason->name?></td>
                </tr>
            <?php }?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">
                    <?= \CHtml::submitButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'add',
                            'data-uri' => '/OphTrOperationbooking/admin/AddPatientUnavailableReason',
                            'id' => 'et_add'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.patientunavailablereasons-enabled').on('change', function() {
            var checkbox = $(this);
            var id = $(this).val();
            var action = 'disabled';
            if ($(this).is(':checked')) {
                action = 'enabled';
            }
            $.ajax({
                'type': 'POST',
                'url': baseUrl+'/OphTrOperationbooking/admin/SwitchEnabledPatientUnavailableReason',
                'data': {id: id, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
                'success': function(resp) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Reason " + action
                    }).open();
                },
                'error': function(resp) {
                    if (checkbox.is(':checked')) {
                        checkbox.prop('checked', false);
                    }
                    else {
                        checkbox.prop('checked', true);
                    }
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Unexpected error"
                    }).open();
                }
            });
        });
    });

</script>
