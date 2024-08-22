<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation, 2020
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
 */
?>
<div class="element-data flex-layout flex-left col-gap" data-test="hfa-view">
    <?php
    if ($this->element) {
        echo \CHtml::activeHiddenField($this->element, "id");
    }

    $sidedData = $this->element->getSidedData();
    foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) {
        ?>
        <div class="<?= $eye_side ?>-eye cols-full" data-side="<?= $eye_side ?>">
            <?php
            if (count($sidedData[$eye_side]) > 0) {
                $side = $sidedData[$eye_side][0];
                ?>
                    <table>
                    <tr>
                        <td class="data-label">

                            <?= $element->getAttributeLabel('mean_deviation'); ?>
                        </td>
                        <td>
                            <div class="element-data">
                                <div class="data-value"><?= $side->mean_deviation; ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="data-label">
                            <?= $element->getAttributeLabel('visual_field_index'); ?>
                        </td>
                        <td>
                            <div class="element-data">
                                <div class="data-value"><?= $side->visual_field_index; ?></div>
                            </div>
                        </td>
                    </tr>
                </table> 
                <?php
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>
