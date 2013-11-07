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
	<h2><?php echo $theatre->id ? 'Edit' : 'Add'?> theatre</h2>
	<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'adminform',
			'enableAjaxValidation'=>false,
			'focus'=>'#username'
		))?>
	<?php echo $form->dropDownList($theatre,'site_id',Site::model()->getListForCurrentInstitution(),array('empty'=>'- Site -'))?>
	<?php echo $form->textField($theatre,'name')?>
	<?php echo $form->textField($theatre,'code',array('size'=>10))?>
	<?php echo $form->dropDownList($theatre,'ward_id',CHtml::listData(OphTrOperationbooking_Operation_Ward::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
	<?php $this->endWidget()?>
</div>
<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>

<?php echo EventAction::button('Save', 'save', null , array('class' => 'small'))->toHtml()?>
<?php echo EventAction::button('Cancel', 'cancel', array('level' => 'warning'), array('class' => 'small'))->toHtml()?>
<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />

<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewTheatres';
	});
	handleButton($('#et_save'),function(e) {
		$('#adminform').submit();
	});
</script>
