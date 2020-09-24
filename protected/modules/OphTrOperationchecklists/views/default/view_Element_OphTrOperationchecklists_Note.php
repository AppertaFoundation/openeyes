<?php
/**
 * (C) Copyright Apperta Foundation, 2020
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

/**
 * @var Element_OphTrOperationchecklists_Note $element
 */
?>

<div class="element-data full-width">
    <div>
        <table class="cols-full large-text">
            <colgroup>
                <col class="cols-6">
                <col class="cols-6">
            </colgroup>
            <tbody>
            <?php foreach ($element->notes as $notes) {
                $text = $notes->notes;
                $toolTip = "Created: " . $notes->created_date . " by " . $notes->createdUser->getFullNameAndTitle();
                ?>
                <tr>
                    <td>
                        <span class="cols-5"><?= $text; ?></span>
                    </td>
                    <td>
                        <i class="oe-i info small js-has-tooltip" data-tooltip-content="<?= $toolTip; ?>"></i>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>