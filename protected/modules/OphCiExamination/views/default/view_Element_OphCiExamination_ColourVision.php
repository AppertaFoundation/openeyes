<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-data element-eyes flex-layout">
    <div class="js-element-eye right-eye cols-6">
        <div class="data-group">
            <?php if ($element->hasRight()) { ?>
                <table class="element-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($element->right_readings as $reading) {
                            ?>
                            <tr>
                                <td><?php echo $reading->method->name ?></td>
                                <td><?php echo $reading->value->name ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="data-value not-recorded">None given</div>
                <?php } ?>
        </div>
    </div>
    <div class="js-element-eye left-eye cols-6">
        <div class="data-group">
            <?php if ($element->hasLeft()) { ?>
                <table class="element-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($element->left_readings as $reading) {
                            ?>
                            <tr>
                                <td><?php echo $reading->method->name ?></td>
                                <td><?php echo $reading->value->name ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                ?>
                <div class="data-value not-recorded">None given</div>
                <?php }
            ?>
        </div>
    </div>
</div>
