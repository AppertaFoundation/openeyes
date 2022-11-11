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

<section class="element view">
    <header class="element-header">
        <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
    </header>
    <section class="element-data full-width">
        <div class="eyedraw flex-layout">
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Cataract',
                'side' => $element->eye->getShortName(),
                'mode' => 'view',
                'width' => $this->action->id === 'view' ? 200 : 120,
                'height' => $this->action->id === 'view' ? 200 : 120,
                'model' => $element,
                'attribute' => 'eyedraw',
                'idSuffix' => 'Cataract',
            ));
            ?>
        <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Position',
                'side' => $element->eye->getShortName(),
                'mode' => 'view',
                'width' => $this->action->id === 'view' ? 200 : 120,
                'height' => $this->action->id === 'view' ? 200 : 120,
                'model' => $element,
                'attribute' => 'eyedraw2',
            ));
            ?>
            <div class="eyedraw-data" style="max-width: 50%">
                <table class="label-value no-lines last-left">
                    <colgroup>
                        <col class="cols-5">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('incision_site_id')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?php echo $element->incision_site->name ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('length')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?php echo $element->length ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('meridian')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?php echo $element->meridian ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('incision_type_id')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?php echo $element->incision_type->name ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('report2')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?=\CHtml::encode($element->report2) . '<br>' . CHtml::encode($element->comments) ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('iol_type_id')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value">
                                <?php echo $element->iol_type ? $element->iol_type->display_name : 'None'; ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('iol_power')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?=\CHtml::encode($element->iol_power) ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div
                                    class="data-label"><?=\CHtml::encode($element->getAttributeLabel('predicted_refraction')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?=\CHtml::encode($element->predicted_refraction) ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('iol_position_id')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?=\CHtml::encode($element->iol_position->name) ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('phaco_cde')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value <?= $element->phaco_cde == "" ? 'not-recorded' : '' ?>">
                                <?= $element->phaco_cde == "" ? 'Not recorded' : CHtml::encode($element->phaco_cde) ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('pcr_risk')) ?>
                                :
                            </div>
                        </td>
                        <td>
                            <div class="data-value"><?=\CHtml::encode($element->pcr_risk) ?>%</div>
                        </td>
                    </tr>


                    <?php if ($element->getSetting('fife')) : ?>
                        <tr>
                            <td>
                                <div class="data-label">
                                    <?=\CHtml::encode($element->getAttributeLabel('intraocular_solution_id')) ?>
                                </div>
                            </td>
                            <td>
                                <div
                                        class="data-value"><?php echo $element->intraocular_solution ? $element->intraocular_solution->name : 'Not specified' ?></div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="data-label">
                                    <?=\CHtml::encode($element->getAttributeLabel('skin_preparation_id')) ?>
                                </div>
                            </td>
                            <td>
                                <div class="data-value">
                                    <?php echo $element->skin_preparation ? $element->skin_preparation->name : 'Not specified' ?>
                                </div>
                            </td>
                        </tr>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
</section>

<div class="flex-layout flex-left flex-stretch">
    <section class="element view tile priority view-agents">
        <header class="element-header">
            <h3 class="element-title">Agent(s)</h3>
        </header>
        <div class="element-data full-width">
            <div class="data-value">
                <div class="tile-data-overflow">
                    <?php if (!$element->operative_devices) { ?>
                        None
                    <?php } else { ?>
                        <table class="large last-left">
                            <tbody>
                            <?php foreach ($element->operative_devices as $device) { ?>
                                <tr>
                                    <td><?php echo $device->name ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <section class="element view tile priority view-cataract-complications">
        <header class="element-header">
            <h3 class="element-title">Cataract complications</h3>
        </header>
        <div class="element-data full-width">
            <div class="data-value">
                <div class="tile-data-overflow">
                    <?php if (!$element->complications && !$element->complication_notes) { ?>
                        None
                    <?php } else { ?>
                        <table class="large last-left">
                            <tbody>
                            <?php foreach ($element->complications as $complication) { ?>
                                <tr>
                                    <td><?php echo $complication->name ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?=\CHtml::encode($element->complication_notes) ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>
