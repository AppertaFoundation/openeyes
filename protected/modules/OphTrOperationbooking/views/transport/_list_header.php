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
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
?>
<div id="no_gp_warning" class="alert-box alert with-icon hide">
    One or more patients has no <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> practice, please correct in PAS before printing <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> letter.
</div>
<div id="transportList">
    <table class="standard transport">
        <thead>
            <tr>
                <th><?= PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id) ?></th>
                <th>Patient</th>
                <th>TCI date</th>
                <th>Admission time</th>
                <th>Site</th>
                <th>Ward</th>
                <th>Method</th>
                <th>Firm</th>
                <th>Subspecialty</th>
                <th>DTA</th>
                <th>Priority</th>
                <th><input type="checkbox" id="transport_checkall" value="" /></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="12">
          <i class="spinner" title="Loading...">loading data ...</i>
                </td>
            </tr>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="12">
                    <ul class="pagination right" id="yw0">
                        <li class="previous unavailable"><a href="/admin/users">&lt; Previous</a></li>
                        <li class="next unavailable"><a href="/admin/users?page=2">Next &gt;</a></li>
                    </ul>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() { transport_load_tcis(); });
</script>
