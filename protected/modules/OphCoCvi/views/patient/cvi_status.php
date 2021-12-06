<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>


<table class="plain patient-data" >
    <thead>
    <tr>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $info) { ?>
        <tr>
            <td><?php echo Helper::formatFuzzyDate($info['date']); ?></td>
            <td><?php echo \CHtml::encode($info['status']); ?></td>
            <td><?php if (isset($info['event_url'])) {?>
                    <a href="<?=$info['event_url']?>">View</a>
                <?php } elseif ($this->checkAccess('OprnEditOphInfo')) {
                    ?>
                    <button id="btn-edit_oph_info" class="secondary small">
                        Edit
                    </button>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php if ($this->checkAccess('OprnCreateCvi', Yii::app()->user->id)) { ?>
    <div class="box-actions">
        <button id="btn-new-ecvi" class="secondary small">
            Issue new eCVI
        </button>
    </div>
    <script type="text/javascript">
        $('#btn-new-ecvi').click(function() {
            window.location = '<?=\CHtml::encode($new_event_uri) ?>';
        });
    </script>
<?php } ?>
<?php if ($oph_info_editable) {
    $this->renderPartial('//patient/cvi_status_form', array('info' => $oph_info));
}?>
