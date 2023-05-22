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

use OEModule\OphGeneric\models\HFAEntry;

?>
<div class="HFA element-data element-eyes flex-layout flex-left col-gap" data-test="hfa-edit">
    <?php
    echo \CHtml::activeHiddenField($this->element, "id");

    $sidedData = $this->element->getSidedData();
    $index = 0;
    foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) {
        ?>
        <div class="<?= $eye_side ?>-eye cols-full" data-side="<?= $eye_side ?>">
            <?php
            $side = count($sidedData[$eye_side]) > 0 ? $sidedData[$eye_side][0] : null;
            echo \CHtml::hiddenField('OEModule_OphGeneric_models_HFA[hfaEntry][' . $index . '][eye_id]', Eye::getIdFromName($eye_side));
            ?>
            <table>
                <tr>
                    <td class="data-label">
                        <?= HFAEntry::model()->getAttributeLabel('mean_deviation'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?php echo \CHTML::numberField(
                                'OEModule_OphGeneric_models_HFA[hfaEntry][' . $index . '][mean_deviation]',
                                $side ? $side->mean_deviation : '',
                                ['step' => 'any', 'max' => HFAEntry::MEAN_DEVIATION_MAX, 'min' => HFAEntry::MEAN_DEVIATION_MIN]
                            ); ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= HFAEntry::model()->getAttributeLabel('visual_field_index'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?php echo \CHTML::numberField(
                                'OEModule_OphGeneric_models_HFA[hfaEntry][' . $index . '][visual_field_index]',
                                $side ? $side->visual_field_index : '',
                                ['step' => 'any', 'max' => HFAEntry::VISUAL_FIELD_INDEX_MAX, 'min' => HFAEntry::VISUAL_FIELD_INDEX_MIN]
                            ); ?></div>
                        </div>
                    </td>
                </tr>
            </table> 
        </div>
        <?php
        $index++;
    }?>
</div>
