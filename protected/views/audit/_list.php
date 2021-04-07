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
<?php
if (empty($data['items'])) {?>
    <div class="alert-box">
        No audit logs match the search criteria.
    </div>
    <?php
} else {?>
<table class="standard audit-logs">
    <thead>
    <tr>
        <th>Timestamp</th>
        <th>Site</th>
        <th>Firm</th>
        <th>User</th>
        <th>Action</th>
        <th>Target type</th>
        <th>Event type</th>
        <th>Patient</th>
    </tr>
    </thead>
    <tbody id="auditListData">
    <?php foreach ($data['items'] as $i => $log) {
        $this->renderPartial('_list_row', array('i' => $i, 'log' => $log));
    }?>
    </tbody>
</table>
    <div class="pagination last"></div>

<?php } ?>

<script>
    $(document).ready(function() {
        // set hidden input values from index.php needed in audit.js
        $('#previous_site_id').val("<?=\Yii::app()->request->getPost('site_id')?>");
        $('#previous_firm_id').val("<?=\Yii::app()->request->getPost('firm_id')?>");
        $('#previous_user_id').val("<?=\Yii::app()->request->getPost('oe-autocompletesearch')?>");
        $('#previous_action').val("<?=\Yii::app()->request->getPost('action')?>");
        $('#previous_target_type').val("<?=\Yii::app()->request->getPost('target_type')?>");
        $('#previous_event_type_id').val("<?=\Yii::app()->request->getPost('event_type_id')?>");
        $('#previous_date_from').val("<?=\Yii::app()->request->getPost('date_from')?>");
        $('#previous_date_to').val("<?=\Yii::app()->request->getPost('date_to')?>");
        $('#previous_patient_identifier_value').val("<?=\Yii::app()->request->getPost('patient_identifier_value')?>");
    });
</script>