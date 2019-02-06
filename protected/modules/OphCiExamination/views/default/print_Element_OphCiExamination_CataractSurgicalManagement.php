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
<table class="borders">
    <tbody>
        <tr>
            <th class="cols-6"><?= $element->getAttributeLabel('eye_id') ?>:</th>
            <td class="cols-6"><?= $element->eye ? $element->eye->name : 'Eye no specified' ?></td>
        </tr>

        <?php if (isset($active_check) && ($active_check === 'on')) { ?>
            <?php if ($element->city_road) { ?>
            <tr>
                <th><?= $element->getAttributeLabel('city_road')?>:</th>
                <td><?= $element->city_road ? 'Yes' : 'No'?></td>
            </tr>
            <?php } ?>

            <?php if ($element->satellite) { ?>
            <tr>
                <th><?= $element->getAttributeLabel('satellite')?>:</th>
                <td><?= $element->satellite ? 'Yes' : 'No'?></td>
            </tr>
            <?php } ?>

        <?php } ?>

        <?php if ($element->fast_track) { ?>
        <tr>
            <th><?= $element->getAttributeLabel('fast_track') ?>:</th>
            <td><?= $element->fast_track ? 'Yes' : 'No'?></td>
        </tr>
        <?php } ?>

        <tr>
            <th><?= $element->getAttributeLabel('target_postop_refraction')?>:</th>
            <td><?= $element->target_postop_refraction?></td>
        </tr>

        <tr>
            <th><?= $element->getAttributeLabel('correction_discussed')?>:</th>
            <td><?= $element->correction_discussed ? 'Yes' : 'No'?></td>
        </tr>

        <tr>
            <th> <?=$element->getAttributeLabel('suitable_for_surgeon_id')?>:</th>
            <td><?= $element->suitable_for_surgeon->name?></td>
        </tr>
        <tr>
            <th><?php echo $element->getAttributeLabel('supervised')?>:</th>
            <td><?= $element->supervised ? 'Yes' : 'No'?></td>
        </tr>
        <tr>
            <th><?= $element->getAttributeLabel('vitrectomised_eye')?>:</th>
            <td><?= $element->vitrectomised_eye ? 'Yes' : 'No'?></td>
        </tr>
        <tr>
            <th>
                <?= $element->getAttributeLabel('reasonForSurgery')?>:
            </th>
            <td>
                <?php
                if ($element->reasonForSurgery) {
                    foreach ($element->reasonForSurgery as $reason) {
                        echo $reason->name.'<br />';
                    }
                }?>
            </td>
        </tr>
    </tbody>
</table>
