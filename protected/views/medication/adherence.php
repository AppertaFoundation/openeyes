<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php if ($patient->medications) { ?>
    <table class="plain patient-data">
        <tr>
            <th>Adherence</th>
            <td><?=@$patient->adherence ? $patient->adherence->level->name : 'Not Recorded'?></td>
        </tr>
        <tr>
            <th>Comments</th>
            <td><?=@$patient->adherence->comments ? $patient->adherence->textWithLineBreaks('comments') : 'Not Recorded'?></td>
        </tr>
        <?php if ($this->checkAccess('OprnEditMedication')) { ?>
            <tr>
                <th>Actions</th>
                <td>
                    <a href="#" class="medication_edit" data-id="adherence">Edit</a>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>