<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<tr data-key="<?=$row_count?>">
    <td>start date</td>
    <td>end date</td>
    <td><span class="medication-display"><span class="medication-name"></span> <a href="#" class="medication-rename"><i class="fa fa-times-circle" aria-hidden="true" title="Change medication"></i></a></span>
        <input type="hidden" name="<?= $field_prefix ?>[drug_id]" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_id]" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" />
        <input type="text" name="<?= $field_prefix ?>[medication_search]" class="search" placeholder="Type to search" /></td>
    <td>administration</td>
    <td class="edit-column">
        <button class="button small warning remove" <?php if (!$removable) {?>style="display: none;"<?php } ?>>remove</button>
    </td>
</tr>
