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
} elseif (Yii::app()->request->getQuery('patient_id', '0')) {
    $patient_id = Yii::app()->request->getQuery('patient_id');
}

if ($patient_id) {
    $withdrawals = $element->possibleWithdrawalEvents(Patient::model()->findByPk($patient_id));
}

$pedigree = new Pedigree();
?>

<section class="element <?php echo $element->elementType->class_name ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">

  <div class="element-fields">
      <?php 
         /*
         $form->widget('application.widgets.ElementSelection', array(
          'element' => $element,
          'field' => 'withdrawal_source_id',
          'relatedElements' => $withdrawals,
          'htmlOptions' => array('empty' => '- Select -'),
          'layoutColumns' => array('label' => 3, 'field' => 3),
      ));
         */?>
      <?php $form->dropDownList(
          $element,
          'gene_id',
          CHtml::listData(PedigreeGene::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
          array('empty' => '- Select -'),
          false,
          array('label' => 3, 'field' => 3)
      ) ?>
      <?php $form->dropDownList(
          $element,
          'method_id',
          CHtml::listData(OphInGeneticresults_Test_Method::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
          array('empty' => '- Select -'),
          false,
          array('label' => 3, 'field' => 3)
      ) ?>
      <?php $form->dropDownList(
          $element,
          'effect_id',
          CHtml::listData(OphInGeneticresults_Test_Effect::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
          array('empty' => '- Select -'),
          false, array('label' => 3, 'field' => 3)
      ) ?>

      <?php $form->textField($element, 'exon', array(), array(), array('label' => 3, 'field' => 3)) ?>
      <?php $form->dropDownList(
          $element,
          'base_change_id',
          CHtml::listData(PedigreeBaseChangeType::model()->findAll(array('order' => '`change` asc')), 'id', 'change'),
          array('empty' => '- Select -'),
          false, array('label' => 3, 'field' => 3)
      ) ?>
      <?php $form->textField($element, 'base_change', array('class' => 'gene-validation'), array(), array('label' => 3, 'field' => 3)) ?>
      <?php $form->dropDownList(
          $element,
          'amino_acid_change_id',
          CHtml::listData(PedigreeAminoAcidChangeType::model()->findAll(array('order' => '`change` asc')), 'id', 'change'),
          array('empty' => '- Select -'),
          false, array('label' => 3, 'field' => 3)
      ) ?>
      <?php $form->textField($element, 'amino_acid_change', array('class' => 'gene-validation'), array(), array('label' => 3, 'field' => 3)) ?>
      <?php $form->textField($element, 'genomic_coordinate', array(), array(), array('label' => 3, 'field' => 3)) ?>
      <?php $form->dropDownList(
          $element,
          'genome_version',
          array_combine($pedigree->genomeVersions(), $pedigree->genomeVersions()),
          array('empty' => '- Select -'),
          false, array('label' => 3, 'field' => 3)
      ) ?>
      <?php $form->textField($element, 'gene_transcript', array(), array(), array('label' => 3, 'field' => 3)) ?>

      <?php $form->textField($element, 'assay', array(), array(), array('label' => 3, 'field' => 3)) ?>
      <?php $form->radioBoolean($element, 'homo', array(), array('label' => 3, 'field' => 9)) ?>
      <?php $form->textField($element, 'result', array(), array(), array('label' => 3, 'field' => 5)) ?>
      <?php $form->datePicker($element, 'result_date', array(), array(), array('label' => 3, 'field' => 2)) ?>
      <?php $form->textArea($element, 'comments', array(), false, array(), array('label' => 3, 'field' => 5)) ?>
  </div>
</section>
