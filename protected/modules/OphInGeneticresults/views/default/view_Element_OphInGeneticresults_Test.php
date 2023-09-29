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

<div class="element-data full-width">
  <div class="cols-10">
    <table class= "cols-full last-left">
      <colgroup>
        <col class="cols-4">
      </colgroup>
      <tbody>
        <tr>
          <td><?= \CHtml::encode($element->getAttributeLabel('id')) ?></td>
          <td><?= \CHtml::encode($element->id) ?></td>
        </tr>
        <tr>
          <td><?= \CHtml::encode($element->getAttributeLabel('gene_id')) ?></td>
          <td><?= \CHtml::encode($element->gene ? $element->gene->name : 'None') ?></td>
        </tr>
        <tr>
          <td><?= \CHtml::encode($element->getAttributeLabel('method_id')) ?></td>
          <td><?= \CHtml::encode($element->method ? $element->method->name : 'None') ?></td>
        </tr>
        <tr>
          <td><?= \CHtml::encode($element->getAttributeLabel('effect_id')) ?></td>
          <td><?= \CHtml::encode($element->effect ? $element->effect->name : 'None') ?></td>
        </tr>
        <tr>
          <td <?= empty($element->exon) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('exon')) ?>
          </td>
          <td><?= \CHtml::encode($element->exon) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->base_change_id) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('base_change_id')) ?>
          </td>
          <td>
            <?= \CHtml::encode($element->base_change_type ? $element->base_change_type->change : '') ?>
          </td>
        </tr>
        <tr>
          <td <?= empty($element->base_change) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('base_change')) ?>
          </td>
          <td><?= \CHtml::encode($element->base_change) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->amino_acid_change_id) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('amino_acid_change_id')) ?>
          </td>
          <td>
            <?= \CHtml::encode($element->amino_acid_change_type ? $element->amino_acid_change_type->change : '') ?>
          </td>
        </tr>
        <tr>
          <td <?= empty($element->amino_acid_change) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('amino_acid_change')) ?>
          </td>
          <td><?= \CHtml::encode($element->amino_acid_change) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->genome_coordinate) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('genomic_coordinate')) ?>
          </td>
          <td><?= \CHtml::encode($element->genomic_coordinate) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->genome_version) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('genome_version')) ?>
          </td>
          <td><?= \CHtml::encode($element->genome_version) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->gene_transcript) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('gene_transcript')) ?>
          </td>
          <td><?=\CHtml::encode($element->gene_transcript) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->assay) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('assay')) ?>
          </td>
          <td><?= \CHtml::encode($element->assay) ?></td>
        </tr>
        <tr>
          <td><?= \CHtml::encode($element->getAttributeLabel('homo')) ?></td>
          <td><?= $element->homo === '1' ? 'Yes' : 'No' ?></td>
        </tr>
        <tr>
          <td <?= empty($element->result) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('result')) ?>
          </td>
          <td><?= \CHtml::encode($element->result) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->result_date) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('result_date')) ?>
          </td>
          <td><?= \CHtml::encode($element->NHSDate('result_date')) ?></td>
        </tr>
        <tr>
          <td <?= empty($element->comments) ? 'class="fade"' : '' ?>>
            <?= \CHtml::encode($element->getAttributeLabel('comments')) ?>
          </td>
          <td>
            <?= \OELinebreakReplacer::replace(\CHtml::encode($element->comments)) ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
