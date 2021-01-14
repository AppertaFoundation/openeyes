<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var \OEModule\OphCiExamination\models\StrabismusManagement_Entry $entry */
/** @var \OEModule\OphCiExamination\widgets\StrabismusManagement $this */
?>

<tr data-key="<?= $row_count ?>">
    <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
    <input type="hidden" class="js-eye" name="<?= $field_prefix ?>[eye_id]" value="<?= CHtml::encode($entry->eye_id) ?>"/>
    <td><input type="text"
               class="js-treatment"
               name="<?= $field_prefix ?>[treatment]"
               value="<?= CHtml::encode($entry->treatment) ?>" /></td>
    <td><input type="text"
               class="js-treatment-options"
               name="<?= $field_prefix ?>[treatment_options]"
               value="<?= CHtml::encode($entry->treatment_options) ?>" /></td>
    <td><input type="text"
               class="js-treatment-reason"
               name="<?= $field_prefix ?>[treatment_reason]"
               value="<?= CHtml::encode($entry->treatment_reason) ?>"
               style="<?= $entry->treatment_reason ? "" : "display: none" ?>" />
        <span class="none js-none-display" style="<?= $entry->treatment_reason ? "display: none" : "" ?>">None</span></td>
    <td><span class="oe-eye-lat-icons js-laterality"><i class="oe-i laterality R small pad js-right-laterality <?= $entry->hasRight() ? "" : "NA" ?>"></i><i class="oe-i laterality L small pad js-left-laterality <?= $entry->hasLeft() ? "" : "NA" ?>"></i></span></td>
    <td><i class="oe-i trash"></i> </td>
</tr>
