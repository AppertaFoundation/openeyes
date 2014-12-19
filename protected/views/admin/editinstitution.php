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
	<h2>Edit institution</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#username',
		'layoutColumns' => array(
			'label' => 2,
			'field' => 5
		)
	))?>
		<?php echo $form->textField($institution,'name',array('autocomplete'=>Yii::app()->params['html_autocomplete'],'size'=>'50'))?>
		<?php echo $form->textField($institution,'remote_id',array('autocomplete'=>Yii::app()->params['html_autocomplete'],'size'=>'10'))?>
		<fieldset class="field-row">
			<legend><?php echo get_class($address)?></legend>
			<?php echo $form->textField($address,'address1',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
			<?php echo $form->textField($address,'address2',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
			<?php echo $form->textField($address,'city',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
			<?php echo $form->textField($address,'county',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
			<?php echo $form->textField($address,'postcode',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
			<?php echo $form->dropDownList($address,'country_id','Country')?>
		</fieldset>
		<?php echo $form->formActions();?>
	<?php $this->endWidget()?>
</div>

<div class="box admin">
	<h2>Sites</h2>
	<form id="admin_institution_sites">
		<table class="grid">
			<thead>
				<tr>
					<th>ID</th>
					<th>Remote ID</th>
					<th>Name</th>
					<th>Address</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($institution->sites as $site) { ?>
					<tr class="clickable" data-id="<?php echo $site->id?>" data-uri="admin/editsite?site_id=<?php echo $site->id?>">
						<td><?php echo $site->id?></td>
						<td><?php echo $site->remote_id?>&nbsp;</td>
						<td><?php echo $site->name?>&nbsp;</td>
						<td><?php echo $site->getLetterAddress(array('delimiter'=>', '))?>&nbsp;</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</form>
</div>

<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/institutions';
	});
</script>
