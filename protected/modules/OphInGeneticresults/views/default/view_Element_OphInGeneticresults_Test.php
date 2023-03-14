<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="element-fields full-width flex-layout flex-top col-gap">
    <div class="cols-7 data-group">
        <table class= "cols-full">
            <colgroup>
                <col class="cols-5">
                <col class="cols-7">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('id')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->id) ?>
                        </div>
                    </td>
                </tr>
            <tr>
                <td>
                    <div class="data-label">
                        <?=\CHtml::encode($element->getAttributeLabel('gene_id')) ?>
                    </div>
                </td>
                <td>
                    <div class="data-value">
                        <?=\CHtml::encode($element->gene ? $element->gene->name : 'None') ?>
                    </div>
                </td>
            </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('method_id')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->method ? $element->method->name : 'None') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('effect_id')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->effect ? $element->effect->name : 'None') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('exon')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->exon) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('base_change_id')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->base_change_type ? $element->base_change_type->change : '') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('base_change')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->base_change) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('amino_acid_change_id')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->amino_acid_change_type ? $element->amino_acid_change_type->change : '') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('amino_acid_change')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->amino_acid_change) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('genomic_coordinate')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->genomic_coordinate) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('genome_version')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->genome_version) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('gene_transcript')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->gene_transcript) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('assay')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->assay) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('homo')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php echo $element->homo === '1' ? 'Yes' : 'No'; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('result')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->result) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('result_date')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?=\CHtml::encode($element->NHSDate('result_date')) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('comments')) ?>
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?= \OELinebreakReplacer::replace(\CHtml::encode($element->comments)) ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
