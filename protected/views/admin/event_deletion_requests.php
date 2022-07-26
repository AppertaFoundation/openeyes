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
 *
 * @var $institutions array
 * @var $selected_institution int
 * @var $events Event[]
 */
?>

<div class="cols-7">
    <form id="admin_event_deletion_requests">
        <select id="select-institution">
            <?php foreach ($institutions as $institution) {
                if ($institution['id'] === $selected_institution) {
                    echo "<option value=\"{$institution['id']}\" selected>{$institution['name']}</option>";
                } else {
                    echo "<option value=\"{$institution['id']}\">{$institution['name']}</option>";
                }
            } ?>
        </select>

        <input type="hidden" name="YII_CSRF_TOKEN"
               value="<?php echo Yii::app()->request->csrfToken?>" />
        <table class="standard">
            <thead>
                <tr>
                    <th>Date/time</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php
            foreach ($events as $i => $event) {?>
                <tr data-id="<?php echo $event->id?>"
                    data-uri="admin/viewDeletionRequest/<?php echo $event->id?>">
                    <td>
                        <?php echo $event->NHSDate('last_modified_date')?>
                        <?php echo substr($event->last_modified_date, 11, 5)?>
                    </td>
                    <td><?php echo $event->usermodified->fullName?></td>
                    <td>
                        <a href="<?php echo Yii::app()->createUrl('/'.$event->eventType->class_name.'/default/view/'.$event->id)?>">
                            <?php echo $event->eventType->name?>
                            <?php echo $event->id?></a>
                    </td>
                    <td><?php echo $event->delete_reason?></td>
                    <td>
                        <?=\CHtml::submitButton(
                            'Approve',
                            [
                                'class' => 'button large',
                                'id' => 'et_approve',
                                'name' => 'approve'
                            ]
                        );?>
                        <?=\CHtml::submitButton(
                            'Reject',
                            [
                                'class' => 'button large',
                                'id' => 'et_reject',
                                'name' => 'reject'
                            ]
                        );?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#select-institution').change(function() {
            window.location.href = '/admin/eventDeletionRequests?selected_institution=' + $(this).val();
        });
    });
</script>