<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="box admin">
	<h2>Edit pedigree</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#username',
		'layoutColumns'=>array(
			'label' => 2,
			'field' => 4
		)
	))?>
		<?php echo $form->dropDownList($pedigree,'inheritance_id',CHtml::listData(PedigreeInheritance::model()->findAll(array('order'=>'name asc')),'id','name'),array('empty' => '- None -'))?>
		<?php echo $form->dropDownList($pedigree,'gene_id',CHtml::listData(PedigreeGene::model()->findAll(array('order'=>'name asc')),'id','name'),array('empty' => '- None -'))?>
		<?php echo $form->radioBoolean($pedigree,'consanguinity')?>
		<?php $form->widget('application.widgets.DiagnosisSelection',array(
			'field' => 'disorder_id',
			'element' => $pedigree,
			'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
			'layoutColumns' => array(
				'label' => $form->layoutColumns['label'],
				'field' => 4
			),
			'default' => false,
		))?>
		<?php echo $form->textField($pedigree,'base_change')?>
		<?php echo $form->textField($pedigree,'amino_acid_change')?>
		<?php echo $form->textArea($pedigree,'comments')?>
		<?php echo $form->formActions()?>
	<?php $this->endWidget()?>
</div>
