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

$patient_id = null;
$withdrawals = array();

if ($element->event) {
    $patient_id = $element->event->episode->patient_id;
} elseif (\Yii::app()->request->getQuery('patient_id', '0')) {
    $patient_id = \Yii::app()->request->getQuery('patient_id');
}

if ($patient_id) {
    $withdrawals = $element->possibleWithdrawalEvents(Patient::model()->findByPk($patient_id));
}

$pedigree = new Pedigree();
?>

<div class="element-fields full-width flex-layout flex-top col-gap <?= $element->elementType->class_name ?>"
         data-element-type-id="<?= $element->elementType->id ?>"
         data-element-type-class="<?= $element->elementType->class_name ?>"
         data-element-type-name="<?= $element->elementType->name ?>"
         data-element-display-order="<?= $element->elementType->display_order ?>">
  <div class="cols-10 data-group">
    <table class= "cols-full last-left">
      <colgroup>
        <col class="cols-4">
      </colgroup>
      <tbody>
        <tr>
          <td class="required"><?= $element->getAttributeLabel('gene_id') ?></td>
          <td>
            <?= \CHtml::activeDropDownList(
              $element,
              'gene_id',
              \CHtml::listData(PedigreeGene::model()->findAll(['order' => 'name asc']), 'id', 'name'),
              ['empty' => '- Select -']
            ) ?>
          </td>
        </tr>
        <tr>
          <td class="required"><?= $element->getAttributeLabel('method_id') ?></td>
          <td>
            <?= \CHtml::activeDropDownList(
              $element,
              'method_id',
              \CHtml::listData(OphInGeneticresults_Test_Method::model()->findAll(['order' => 'name asc']), 'id', 'name'),
              ['empty' => '- Select -']
            ) ?>
          </td>
        </tr>
        <tr>
          <td class="required"><?= $element->getAttributeLabel('effect_id') ?></td>
          <td>
            <?= \CHtml::activeDropDownList(
              $element,
              'effect_id',
              \CHtml::listData(OphInGeneticresults_Test_Effect::model()->findAll(['order' => 'name asc']), 'id', 'name'),
              ['empty' => '- Select -']
            ) ?>
          </td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('exon') ?></td>
          <td><?= \CHtml::activeTextField($element, 'exon', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('base_change_id') ?></td>
          <td>
            <?= \CHtml::activeDropDownList(
              $element,
              'base_change_id',
              \CHtml::listData(PedigreeBaseChangeType::model()->findAll(['order' => '`change` asc']), 'id', 'change'),
              ['empty' => '- Select -']
            ) ?>
          </td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('base_change') ?></td>
          <td><?= \CHtml::activeTextField($element, 'base_change', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('amino_acid_change_id') ?></td>
          <td>
            <?= \CHtml::activeDropDownList(
              $element,
              'amino_acid_change_id',
              \CHtml::listData(PedigreeAminoAcidChangeType::model()->findAll(['order' => '`change` asc']), 'id', 'change'),
              ['empty' => '- Select -']
            ) ?>
          </td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('amino_acid_change') ?></td>
          <td><?= \CHtml::activeTextField($element, 'amino_acid_change', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('genomic_coordinate') ?></td>
          <td><?= \CHtml::activeTextField($element, 'genomic_coordinate', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('genome_version') ?></td>
          <td>
            <?= \CHtml::activeDropDownList(
              $element,
              'genome_version',
              array_combine($pedigree->genomeVersions(), $pedigree->genomeVersions()),
              ['empty' => '- Select -']
            ) ?>
          </td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('gene_transcript') ?></td>
          <td><?= \CHtml::activeTextField($element, 'gene_transcript', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('assay') ?></td>
          <td><?= \CHtml::activeTextField($element, 'assay', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td class="required"><?= $element->getAttributeLabel('homo') ?></td>
          <td>
            <?= \CHtml::activeRadioButtonList(
              $element,
              'homo',
              [1 => 'Yes', 0 => 'No'],
              [
                'container' => 'fieldset',
                'template' => '{beginLabel}{input} {labelTitle}{endLabel}',
                'labelOptions' => ['class' => 'inline'],
                'separator' => ''
              ]
            ) ?>
          </td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('result') ?></td>
          <td><?= \CHtml::activeTextField($element, 'result', ['class' => 'cols-full']) ?></td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('result_date') ?></td>
          <td>
            <?= \CHtml::activeDateField($element, 'result_date') ?>
          </td>
        </tr>
        <tr>
          <td><?= $element->getAttributeLabel('comments') ?></td>
          <td><?= \CHtml::activeTextArea($element, 'comments', ['class' => 'cols-full']) ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
