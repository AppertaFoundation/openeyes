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
<?php
/** @var OphDrPrescription_ItemTaper $entry */
?>

<tr class="meds-taper col-gap js-taper-row" data-parent-key="<?=$row_count?>" data-taper-key="<?=$taper_count?>">
    <td><i class="oe-i child-arrow small no-click pad"></i><em class="fade">then</em></td>
    <td>
        <input class="cols-2 js-dose input-validate numbers-only decimal" style="display: inline-block;"  type="hidden" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
        <?= $entry->dose ?>
        <?= Chtml::hiddenField($field_prefix . '[frequency_id]', $entry->frequency_id);?>
        <?= isset($entry->frequency) ? $entry->frequency->term : "" ?>
    </td>
    <td>
        <?= Chtml::hiddenField($field_prefix . '[duration_id]', $entry->duration_id);?>
        <?= isset($entry->duration) ? $entry->duration->name : ""?>
    </td>
    <td></td>
    <td>
    </td>
</tr>
