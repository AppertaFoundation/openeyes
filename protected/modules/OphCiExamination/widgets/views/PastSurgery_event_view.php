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
$widget = $this;
?>
<div class="element-data">
    <div class="data-value">
        <div class="tile-data-overflow">
            <?php if ((!$operations || count($operations) === 0) && !$element->no_pastsurgery_date) { ?>
                <div class="data-value not-recorded">Nil recorded this examination</div>
            <?php } elseif ($element->no_pastsurgery_date) { ?>
                <div class="data-value">
                    Patient has had no previous eye surgery or laser treatment
                </div>
            <?php } else { ?>
            <table>
                <colgroup>
                    <col>
                    <col class="cols-fifth">
                    <col class="cols-2">
                </colgroup>
                <tbody> <?php foreach ($operations as $operation) { ?>
                    <tr>
                        <td>
                            <?= array_key_exists(
                                'object',
                                $operation
                            ) ? $operation['object']->operation : $operation['operation']; ?>
                        </td>
                        <td>
                            <?php if (array_key_exists('link', $operation)) { ?>
                                <a href="<?= $operation['link'] ?>"><i class="oe-i direction-right-circle <?= $this->pro_theme ?> small pad"></i></a>
                            <?php } ?>
                        </td>
                        <td class="nowrap">
                            <?php $side = array_key_exists('side', $operation) ? $operation['side']: (array_key_exists('object', $operation) ? $operation['object']->side : ''); ?>
                            <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                        </td>
                        <td>
                            <span class="oe-date">
                                <?= array_key_exists('object', $operation) ?
                                    $operation['object']->getDisplayDate() : Helper::convertFuzzyDate2HTML($operation['date']); ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= array_key_exists('object', $operation) ? $operation['object']->getDisplayHasOperation() : ''; ?></strong>
                        </td>
                    </tr>
                        <?php }
            } ?>
                </tbody>
            </table>
            <?php if (!$element->no_pastsurgery_date) { ?>
                <?= CHtml::encode($element->comments) ?>
            <?php } ?>
        </div>
    </div>
</div>
