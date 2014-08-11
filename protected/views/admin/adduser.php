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
	<h2>Add user</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#username',
		'layoutColumns'=>array(
			'label' => 2,
			'field' => 5
		)
	))?>
		<?php echo $form->textField($user,'username',array('autocomplete'=>Yii::app()->params['html_autocomplete']),array(array(
			'id' => 'lookup_user',
			'title' => 'Lookup user',
			'href' => '#',
		)))?>
		<?php echo $form->textField($user,'title', array('autocomplete'=>Yii::app()->params['html_autocomplete']), null, array('field' => 2))?>
		<?php echo $form->textField($user,'first_name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($user,'last_name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($user,'email',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($user,'role',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($user,'qualifications',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->radioBoolean($user,'active')?>
		<?php echo $form->radioBoolean($user,'global_firm_rights')?>
		<?php echo $form->radioBoolean($user,'is_doctor')?>
		<?php echo $form->passwordField($user,'password',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->passwordConfirmField($user,'Confirm','User[password_repeat]')?>
		<?php echo $form->multiSelectList($user, 'User[roles]', 'roles', 'name', CHtml::listData(Yii::app()->authManager->getRoles(), 'name', 'name'), array(), array('label' => 'Roles', 'empty' => '-- Add --'));?>
		<?php echo $form->formActions();?>
	<?php $this->endWidget()?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#User_username').focus();
	});
</script>
