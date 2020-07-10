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
 * @var Element_OphCiTheatreadmission_ProcedureList $element
 * @var bool $isCollapsable
 */
?>

<?php if (isset($isCollapsable) && $isCollapsable) { ?>
    <header class="subgroup-header">
        <h3><?= $element->getElementTypeName(); ?></h3>
        <div class="viewstate-icon">
            <i class="oe-i small js-element-subgroup-viewstate-btn collapse" data-subgroup="subgroup-procedure-list"></i>
        </div>
    </header>
<?php } ?>
<div class="element-data full-width" id="subgroup-procedure-list" <?= (isset($isCollapsable) && $isCollapsable) ? 'style= "display: none"' : ''?>>
    <div class="cols-10">
        <table class="cols-full last-left large-text">
            <colgroup>
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        Eye
                    </td>
                    <td>
                        <?= \Eye::model()->findByPk($element->eye_id)->name; ?>
                    </td>
                    <td>
                        <table>
                            <thead>
                                <tr>
                                    Procedure
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($element->procedures as $procedure) : ?>
                                <tr>
                                    <td>
                            <span class="priority-text">
                                <?php echo $element->eye->adjective ?>

                                <?php echo $procedure->term ?>
                            </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        Disorder
                    </td>
                    <td>
                        <?= \Disorder::model()->findByPk($element->disorder_id)->term; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Priority
                    </td>
                    <td>
                        <?= \OphTrOperationbooking_Operation_Priority::model()->findByPk($element->priority_id)->name; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>