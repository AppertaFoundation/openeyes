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
	<h2><?php echo $cbs->id ? 'Edit' : 'Add'?> commissioning body service</h2>
	<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#username',
		'layoutColumns' => array(
			'label' => 2,
			'field' => 5
		)
	));

	$criteria = new CDbCriteria();
	$criteria->order = 't.name asc';
	if ($commissioning_bt) {
		$criteria->addColumnCondition(array('commissioning_body_type_id' => $commissioning_bt->id));
	}
	echo $form->dropDownList($cbs, 'commissioning_body_id', CHtml::listData(CommissioningBody::model()->findAll($criteria), 'id', 'name'), array('style' => 'margin-bottom:6px;'));

	if ($commissioning_bst) { ?>
		<div id="div_CommissioningBodyService_commissioning_body_service_type_id" class="row field-row">
			<div class="large-2 column">
				<label for="div_CommissioningBodyService_commissioning_body_service_type_id">Service type:</label>
			</div>
			<div class="large-5 column end">
				<?php
				echo $form->hiddenInput($cbs, 'commissioning_body_service_type_id', $commissioning_bst->id);
				echo $commissioning_bst->name;
				?>
			</div>
		</div>
	<?php
	} else {
		echo $form->dropDownList($cbs,'commissioning_body_service_type_id','CommissioningBodyServiceType',array('style'=>'margin-bottom:6px;'));
	}
	?>
		<?php echo $form->textField($cbs,'name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($cbs,'code',array('autocomplete'=>Yii::app()->params['html_autocomplete']),null,array('field'=>2))?>
		<?php 
			if(!$cbs->contact)
			{
				$cbs->contact = new Contact();
			}echo $form->textField($cbs->contact,'primary_phone',array('autocomplete'=>Yii::app()->params['html_autocomplete']),null,array('field'=>2));
		?>
		<?php echo $form->textField($address,'address1',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($address,'address2',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($address,'city',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($address,'county',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($address,'postcode',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->dropDownList($address,'country_id','Country')?>
		<?php echo $form->formActions(array('cancel-uri' => $return_url));?>
	<?php $this->endWidget()?>
</div>

