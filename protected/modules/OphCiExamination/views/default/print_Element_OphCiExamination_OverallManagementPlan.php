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
<div class="element-data eye-divider">

    <div class="element-both-eyes">
        <div class="data-value flex-layout flex-top">
            <div class="cols-12">
                <table class="cols-full last-left">
                    <thead>
                        <tr>
                            <th><?= \CHtml::encode($element->getAttributeLabel('clinic_interval_id')) ?></th>
                            <th><?= \CHtml::encode($element->getAttributeLabel('photo_id')) ?></th>
                            <th><?= \CHtml::encode($element->getAttributeLabel('oct_id')) ?></th>
                            <th><?= \CHtml::encode($element->getAttributeLabel('hfa_id')) ?></th>
                            <th><?= \CHtml::encode($element->getAttributeLabel('gonio_id')) ?></th>
                            <th><?= \CHtml::encode($element->getAttributeLabel('hrt_id')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="large-text"><?php echo $element->clinic_interval ? : 'None' ?></span></td>
                            <td><span class="large-text"><?php echo $element->photo ? $element->photo->name : 'None' ?></span></td>
                            <td><span class="large-text"><?php echo $element->oct ? $element->oct->name : 'None' ?></span></td>
                            <td><span class="large-text"><?php echo $element->hfa ? $element->hfa->name : 'None' ?></span></td>
                            <td><span class="large-text"><?php echo $element->gonio ? $element->gonio->name : 'None' ?></span></td>
                            <td><span class="large-text"><?php echo $element->hrt ? $element->hrt->name : 'None' ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($element->comments !== '') : ?>
            <div class="flex-layout">
                <div class="data-label cols-3"><?= \CHtml::encode($element->getAttributeLabel('comments')) ?>:</div>
                <div class="data-value cols-9"><?php echo $element->textWithLineBreaks('comments') ?></div>
            </div>
        <?php endif; ?>
    </div>
    <div class="element-eyes flex-layout">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
            <div class="js-element-eye cols-6  <?= $eye_side ?>-eye">
                <div class="data-group">
                    <?php if ($element->hasEye($eye_side)) : ?>
                        <table class="large-text last-left">
                            <tbody>
                                <tr>
                                    <td><?= \CHtml::encode($element->getAttributeLabel($eye_side . '_target_iop_id')) ?></td>
                                    <td><?php echo $element->{$eye_side . '_target_iop'}->name ?> mmHg</td>
                                </tr>
                            </tbody>
                        </table>

                    <?php else : ?>
                        Not recorded
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


