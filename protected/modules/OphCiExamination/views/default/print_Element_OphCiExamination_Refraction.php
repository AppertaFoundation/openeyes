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
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div class="cols-6 js-element-eye <?= $eye_side ?>-eye">
            <table>
                <tbody>
                    <tr>
                        <?php if ($element->hasEye($eye_side)) : ?>
                            <td>
                                <?= Yii::app()->format->text($element->getCombined($eye_side)) ?>
                            </td>
                            <td>
                                SE:
                                <?= number_format($element->{$eye_side . '_sphere'} + 0.5 * $element->{$eye_side . '_cylinder'}, 2)
                                ?>
                            </td>
                            <td><?= Yii::app()->format->text($element->getType($eye_side)) ?></td>
                            <?php if ($element->{$eye_side . '_notes'}) : ?>
                                <td>
                                    <?php echo $element->textWithLineBreaks($eye_side . '_notes') ?>
                                </td>
                            <?php endif; ?>
                        <?php else : ?>
                            <td>
                                Not recorded
                            </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>
