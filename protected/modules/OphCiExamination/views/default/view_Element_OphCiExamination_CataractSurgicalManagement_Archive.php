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
<div class="element-data full-width">
    <div class="data-group">
        <table class="element-table">
            <tbody>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('eye_id')?>:</td>
                    <td><?php echo $element->eye ? $element->eye->name : 'Not specified'?></td>
                </tr>
        <?php if (isset($active_check) && ($active_check === 'on')) :?>
          <tr>
            <td scope="row"><?php echo $element->getAttributeLabel('city_road')?>:</td>
            <td><?php echo $element->city_road ? 'Yes' : 'No'?></td>
          </tr>
          <tr>
            <td scope="row"><?php echo $element->getAttributeLabel('satellite')?>:</td>
            <td><?php echo $element->satellite ? 'Yes' : 'No'?></td>
          </tr>
        <?php endif ?>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('fast_track')?>:</td>
                    <td><?php echo $element->fast_track ? 'Yes' : 'No'?></td>
                </tr>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('target_postop_refraction')?>:</td>
                    <td><?php echo $element->target_postop_refraction?></td>
                </tr>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('correction_discussed')?>:</td>
                    <td><?php echo $element->correction_discussed ? 'Yes' : 'No'?></td>
                </tr>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('suitable_for_surgeon_id')?>:</td>
                    <td><?php echo $element->suitable_for_surgeon->name?></td>
                </tr>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('supervised')?>:</td>
                    <td><?php echo $element->supervised ? 'Yes' : 'No'?></td>
                </tr>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('previous_refractive_surgery')?>:</td>
                    <td><?php echo $element->previous_refractive_surgery ? 'Yes' : 'No'?></td>
                </tr>
                <tr>
                    <td scope="row"><?php echo $element->getAttributeLabel('vitrectomised_eye')?>:</td>
                    <td><?php echo $element->vitrectomised_eye ? 'Yes' : 'No'?></td>
                </tr>
                <tr>
                    <td scope="row" class="flex-layout flex-top">
                        <?php echo $element->getAttributeLabel('reasonForSurgery')?>:
                    </td>
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
    </div>
</div>
