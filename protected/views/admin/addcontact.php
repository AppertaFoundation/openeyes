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
	<h2>Add contact</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#contactname',
		'layoutColumns' => array(
			'label' => 2,
			'field' => 5
		)
	))?>
		<?php echo $form->textField($contact,'title', array('autocomplete' => Yii::app()->params['html_autocomplete']), null, array('field' => 2))?>
		<?php echo $form->textField($contact,'first_name',array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'last_name',array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'nick_name',array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'primary_phone',array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'qualifications',array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->dropDownList($contact,'contact_label_id',CHtml::listData(ContactLabel::model()->active()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>

		<?php /* TODO */ ?>
		<div class="row field-row hide">
			<div class="large-5 large-offset-2 column">
				<?php echo EventAction::button('Add label','add_label',array(), array('class' => 'small'))->toHtml()?>
			</div>
		</div>
		<?php /* TODO */ ?>

		<?php echo $form->formActions(array('cancel-uri' => '/admin/contacts'))?>
		<?php $this->endWidget()?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#Contact_title').focus();
	});
</script>
