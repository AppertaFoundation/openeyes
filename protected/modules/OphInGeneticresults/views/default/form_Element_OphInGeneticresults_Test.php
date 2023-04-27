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

<div class="element-fields full-width flex-layout flex-top col-gap <?php echo $element->elementType->class_name ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">

  <div class="cols-6 data-group">
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

      <table class= "cols-full">
          <tbody>
            <tr>
                <td>
                    <?php $form->dropDownList(
                        $element,
                        'gene_id',
                        CHtml::listData(PedigreeGene::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                        array('empty' => '- Select -', 'style' => 'width: 100%'),
                        false,
                        array('label' => 7, 'field' => 5)
                    ) ?>
                </td>
            </tr>
          <tr>
              <td>
                  <?php $form->dropDownList(
                      $element,
                      'method_id',
                      CHtml::listData(OphInGeneticresults_Test_Method::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                      array('empty' => '- Select -', 'style' => 'width: 100%'),
                      false,
                      array('label' => 7, 'field' => 5)
                  ) ?>
              </td>
          </tr>
          <tr>
              <td>
                  <?php $form->dropDownList(
                      $element,
                      'effect_id',
                      CHtml::listData(OphInGeneticresults_Test_Effect::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                      array('empty' => '- Select -', 'style' => 'width: 100%'),
                      false,
                      array('label' => 7, 'field' => 5)
                  ) ?>
              </td>
          </tr>
          <tr>
              <td>
                  <?php $form->textField($element, 'exon', array('style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
              </td>
          </tr>
            <tr>
                <td>
                    <?php $form->dropDownList(
                        $element,
                        'base_change_id',
                        CHtml::listData(PedigreeBaseChangeType::model()->findAll(array('order' => '`change` asc')), 'id', 'change'),
                        array('empty' => '- Select -', 'style' => 'width: 100%'),
                        false,
                        array('label' => 7, 'field' => 5)
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textField($element, 'base_change', array('class' => 'gene-validation', 'style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->dropDownList(
                        $element,
                        'amino_acid_change_id',
                        CHtml::listData(PedigreeAminoAcidChangeType::model()->findAll(array('order' => '`change` asc')), 'id', 'change'),
                        array('empty' => '- Select -', 'style' => 'width: 100%'),
                        false,
                        array('label' => 7, 'field' => 5)
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textField($element, 'amino_acid_change', array('class' => 'gene-validation', 'style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textField($element, 'genomic_coordinate', array('style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->dropDownList(
                        $element,
                        'genome_version',
                        array_combine($pedigree->genomeVersions(), $pedigree->genomeVersions()),
                        array('empty' => '- Select -', 'style' => 'width: 100%'),
                        false,
                        array('label' => 7, 'field' => 5)
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textField($element, 'gene_transcript', array('style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textField($element, 'assay', array('style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->radioBoolean($element, 'homo', array('style' => 'width: 100%'), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textField($element, 'result', array('style' => 'width: 100%'), array(), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->datePicker($element, 'result_date', array(), array('style' => 'width: 100%'), array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $form->textArea($element, 'comments', array(), false, ['class' => 'cols-full autosize'], array('label' => 7, 'field' => 5)) ?>
                </td>
            </tr>

          </tbody>
      </table>
  </div>
</div>
