<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-data element-eyes flex-layout">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) { ?>
    <table class="label-item cols-6 <?= $eye_side ?>-eye">
        <?php if ($element->hasEye($eye_side)) {
            if ($element->getCombined($eye_side)) { ?>
                    <tr>
                        <td class="priority-text"><?= $element->getCombined($eye_side) ?></td>
                    </tr>
            <?php } else { ?>
                    <tr>
                        <td>
                            Not recorded
                            <?php if ($element->{$eye_side . '_unable_to_assess'}) { ?>
                                (Unable to assess<?php if ($element->{$eye_side . '_eye_missing'}) {
                                    ?>, eye missing<?php
                                                 } ?>)
                            <?php } elseif ($element->{$eye_side . '_eye_missing'}) { ?>
                                (Eye missing)
                            <?php } ?>
                        </td>
                    </tr>
            <?php } ?>
        <?php } else {  ?>
                <tr>
                    <td>Not recorded</td>
                </tr>
        <?php } ?>
    </table>
    <?php } ?>
</div>
