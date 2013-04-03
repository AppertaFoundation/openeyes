<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?><div class="form">

<?php 
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'phrase-by-firm-form',
	'enableAjaxValidation'=>false,
)); 

if (isset($_GET['section_id'])) {
	$model->section_id = $_GET['section_id'];
}
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

        <?php if (!$model->id) {?>
        <div class="row">
                <?php
                        if ($overrideableNames = PhraseByFirm::Model()->getOverrideableNames($_GET['section_id'], Firm::Model()->findByPk($this->selectedFirmId)->id)) {
                                echo $form->labelEx($model,'phrase_name_id');
                                echo $form->dropDownList($model,'phrase_name_id',CHtml::listData($overrideableNames, 'id', 'name'),array('prompt' => 'Override a global phrase name'));
                                echo $form->error($model,'phrase_name_id');
                        }
                ?>

                <?php
                        echo CHtml::label('New phrase name', 'name');
                        echo CHtml::textField('PhraseName','');
                ?>

        </div>
        <?php } else { ?>
        <div class="row">
                <?php echo $form->labelEx($model,'phrase_name_id'); ?>
                <?php echo $model->name->name; ?>
                <?php echo $form->error($model,'phrase_name_id'); ?>
        </div>
        <?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model,'phrase'); ?>
		<?php echo $form->textArea($model,'phrase',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'phrase'); ?>
	</div>

        <div class="row">
                <?php echo $form->labelEx($model,'section_id'); ?>
                <?php if (!$model->id) { ?>
                        <?php echo Section::Model()->findByPk($_GET['section_id'])->name; ?>
                        <?php echo CHtml::activeHiddenField($model,'section_id',array('value'=>$_GET['section_id'])); ?>
                <?php } else { ?>
                        <?php echo Section::Model()->findByPk($model->section_id)->name; ?>
                        <?php echo CHtml::activeHiddenField($model,'section_id',array('value'=>$model->section_id)); ?>
                <?php } ?>
        </div>


        <div class="row">
                <?php echo $form->labelEx($model,'firm_id'); ?>
                <?php if (!$model->id) { ?>
                        <?php echo Firm::Model()->findByPk($this->selectedFirmId)->name; ?>
                        <?php echo CHtml::activeHiddenField($model,'firm_id',array('value'=>Firm::Model()->findByPk($this->selectedFirmId)->id)); ?>
                <?php } else { ?>
                        <?php echo Firm::Model()->findByPk($model->firm_id)->name; ?>
                        <?php echo CHtml::activeHiddenField($model,'firm_id',array('value'=>$model->firm_id)); ?>
                <?php } ?>
        </div>

	<div class="row">
		<?php echo $form->labelEx($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'display_order'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
