<?php
/**
 * (C) Apperta Foundation, 2020
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
 * @var \OEModule\OphCiExamination\models\Retinoscopy $element
 * @var \OEModule\OphCiExamination\widgets\Retinoscopy $this
 */
?>

<div class="element-data element-eyes">
    <?php foreach (['right', 'left'] as $side) { ?>
        <div class="<?= $side ?>-eye" data-side="<?= $side ?>">
            <?php if ($element->{"has" . ucfirst($side)}()) { ?>
                <div class="cols-12 flex-layout flex-top">
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => $side . '_retinoscopy_' . $element->id,
                        'side' => $side === 'right' ? 'R' : 'L',
                        'mode' => 'view',
                        'width' => 150,
                        'height' => 150,
                        'model' => $element,
                        'attribute' => "{$side}_eyedraw",
                        'toggleScale' => 0.72
                    ));
                    ?>

                    <!-- using tables in here for better layout control -->
                    <div class="cols-6">
                        <table>
                            <tbody><tr>
                                <td></td>
                                <td><?= $element->{"display_{$side}_dilated"} ?></td>
                            </tr>
                            <tr>
                                <td><?= $element->getAttributeLabel("{$side}_working_distance_id") ?></td>
                                <td><?= CHtml::encode($element->{"{$side}_working_distance"}) ?></td>
                            </tr>
                            <tr>
                                <td><?= $element->getAttributeLabel("{$side}_angle") ?></td>
                                <td><?= CHtml::encode($element->{"{$side}_angle"}) ?></td>
                            </tr>
                            <tr>
                                <td><?= $element->getAttributeLabel("{$side}_power1") ?></td>
                                <td><?= $element->{"display_{$side}_power1"} ?></td>
                            </tr>
                            <tr>
                                <td><?= $element->getAttributeLabel("{$side}_power2") ?></td>
                                <td><?= $element->{"display_{$side}_power2"} ?></td>
                            </tr>
                            <tr>
                                <td><?= $element->getAttributeLabel("{$side}_refraction") ?></td>
                                <td><?= CHtml::encode($element->{"{$side}_refraction" }) ?></td>
                            </tr>
                            <tr>
                                <td><?= $element->getAttributeLabel("{$side}_comments") ?></td>
                                <td data-test="retinoscopy-<?= $side ?>-comment"><?= CHtml::encode($element->{"{$side}_comments" }) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div><!-- flex -->
            <?php } else { ?>
                <div class="data-value not-recorded">
                    Not recorded
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
