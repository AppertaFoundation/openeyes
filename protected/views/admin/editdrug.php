<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
  <h2>Edit Drug</h2>
    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#aliases',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 4,
        ),
    )) ?>
    <?php echo $form->textField($drug, 'name', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'disabled' => true)) ?>
    <?php echo $form->textField($drug, 'tallman', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'disabled' => true)) ?>
    <?php echo $form->textField($drug, 'aliases', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
    <?php echo $form->dropDownList($drug, 'type_id', 'DrugType') ?>
    <?php echo $form->textField($drug, 'default_dose', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
    <?php echo $form->textField($drug, 'dose_unit', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
    <?php echo $form->dropDownList($drug, 'default_frequency_id', 'DrugFrequency', array('empty' => '')) ?>
    <?php echo $form->dropDownList($drug, 'default_duration_id', 'DrugDuration', array('empty' => '')) ?>
    <?php echo $form->multiSelectList(
        $drug,
        'allergies',
        'allergies',
        'id',
        CHtml::listData(Allergy::model()->active()->findAll(array('order' => 'name')), 'id', 'name'),
        null,
        array('empty' => '', 'label' => 'Allergies')
    ) ?>
    <?php echo $form->formActions(array('cancel-uri' => '/admin/drugs')) ?>
    <?php $this->endWidget() ?>
</div>
