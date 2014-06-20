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
?>
<section class="box patient-info associated-data js-toggle-container">
	<header class="box-header">
		<h3 class="box-title">
			<span class="icon-patient-clinician-hd_flag"></span>
			Previous ophthalmic surgery
		</h3>
		<a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
			Show/hide this section
			</span>
		</a>
	</header>

	<div class="js-toggle-body">
		<table class="plain patient-data">
			<thead>
			<tr>
				<th>Date</th>
				<th>Operation</th>
				<?php if ($this->checkAccess('OprnEditPreviousOperation')) { ?><th>Actions</th><?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->patient->previousOperations as $operation) {?>
				<tr>
					<td><?php echo $operation->dateText?></td>
					<td><?php if ($operation->side) { echo $operation->side->adjective.' ';}?><?php echo CHtml::encode($operation->operation)?></td>
					<?php if ($this->checkAccess('OprnEditPreviousOperation')): ?>
						<td>
							<a href="#" class="editOperation" rel="<?php echo $operation->id?>">Edit</a>&nbsp;&nbsp;
							<a href="#" class="removeOperation" rel="<?php echo $operation->id?>">Remove</a>
						</td>
					<?php endif ?>
				</tr>
			<?php }?>
			</tbody>
		</table>

		<?php if ($this->checkAccess('OprnEditPreviousOperation')) {?>
			<div class="box-actions">
				<button  id="btn-add_previous_operation" class="secondary small">
					Add Previous ophthalmic surgery
				</button>
			</div>

			<div id="add_previous_operation" style="display: none;">

				<?php
				$form = $this->beginWidget('FormLayout', array(
						'id'=>'add-previous_operation',
						'enableAjaxValidation'=>false,
						'htmlOptions' => array('class'=>'form add-data'),
						'action'=>array('patient/addPreviousOperation'),
						'layoutColumns'=>array(
							'label' => 3,
							'field' => 9
						),
					))?>

				<fieldset class="field-row">

					<legend><strong>Add previous ophthalmic surgery</strong></legend>

					<input type="hidden" name="edit_operation_id" id="edit_operation_id" value="" />
					<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

					<div class="field-row row">
						<div class="<?php echo $form->columns('label');?>">
							<label for="common_previous_operation">Common operations:</label>
						</div>
						<div class="<?php echo $form->columns('field');?>">
							<?php echo CHtml::dropDownList('common_previous_operation','',CHtml::listData(CommonPreviousOperation::model()->findAll(array('order'=>'name asc')),'id','name'),array('empty'=>'- Select -'))?>
						</div>
					</div>

					<div class="field-row row">
						<div class="<?php echo $form->columns('label');?>">
							<label for="previous_operation">Operation:</label>
						</div>
						<div class="<?php echo $form->columns('field');?>">
							<?php echo CHtml::textField('previous_operation','')?>
						</div>
					</div>

					<fieldset class="row field-row">
						<legend class="<?php echo $form->columns('label');?>">
							Side:
						</legend>
						<div class="<?php echo $form->columns('field');?>">
							<label class="inline">
								<input type="radio" name="previous_operation_side" class="previous_operation_side" value="" checked="checked" /> None
							</label>
							<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {?>
								<label class="inline"><input type="radio" name="previous_operation_side" class="previous_operation_side" value="<?php echo $eye->id?>" /> <?php echo $eye->name?>	</label>
							<?php }?>
						</div>
					</fieldset>

					<?php $this->renderPartial('_fuzzy_date',array('class'=>'previousOperation'))?>

					<div class="previous_operations_form_errors alert-box alert hide"></div>

					<div class="buttons">
						<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_previous_operation_loader" style="display: none;" />
						<button type="submit" class="secondary small btn_save_previous_operation">
							Save
						</button>
						<button class="warning small btn_cancel_previous_operation">
							Cancel
						</button>
						</div>
				</fieldset>
				<?php $this->endWidget()?>
			</div>
		<?php }?>
	</div>
</section>

<!-- Confirm deletion dialog -->
<div id="confirm_remove_operation_dialog" title="Confirm remove operation" style="display: none;">
	<div id="delete_operation">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the operation from the patient record.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="operation_id" value="" />
			<button type="submit" class="warning small btn_remove_operation">Remove operation</button>
			<button type="submit" class="secondary small btn_cancel_remove_operation">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#btn-add_previous_operation').click(function() {
		$('#previous_operation').val('');
		$('#edit_operation_id').val('');
		$('div.previousOperation input[name="previous_operation_side"][value=""]').attr('checked','checked');
		var d = new Date;
		$('div.previousOperation select[name="fuzzy_year"]').val(d.getFullYear());
		$('#add_previous_operation').slideToggle('fast');
		$('#btn-add_previous_operation').attr('disabled',true);
		$('#btn-add_previous_operation').addClass('disabled');
	});
	$('button.btn_cancel_previous_operation').click(function() {
		$('#add_previous_operation').slideToggle('fast');
		$('#btn-add_previous_operation').attr('disabled',false);
		$('#btn-add_previous_operation').removeClass('disabled');
		return false;
	});
	$('#common_previous_operation').change(function() {
		$('#previous_operation').val($(this).children('option:selected').text());
		$(this).val(0);
	});
	$('button.btn_save_previous_operation').click(function() {
		if ($('#previous_operation').val().length <1) {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please enter an operation."
			}).open();
			return false;
		}
		$('img.add_previous_operation_loader').show();

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/patient/addPreviousOperation',
			'dataType': 'json',
			'data': $('#add-previous_operation').serialize(),
			'success': function(errors) {
				var ok = true;

				$('.previous_operations_form_errors').html('').hide();

				for (var i in errors) {
					ok = false;
					$('div.previous_operations_form_errors').show().append('<div>'+errors[i]+'</div>');
				}

				$('img.add_previous_operation_loader').hide();

				if (ok) {
					window.location.reload();
				}
			}
		});

		return false;
	});
	$('a.editOperation').click(function(e) {
		var operation_id = $(this).attr('rel');

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/getPreviousOperation?operation_id='+operation_id,
			'success': function(data) {
				$('#edit_operation_id').val(operation_id);
				$('#previous_operation').val(data['operation']);
				$('#add_previous_operation input[name="previous_operation_side"][value="'+data['side_id']+'"]').attr('checked','checked');
				$('#add_previous_operation select[name="fuzzy_day"]').val(data['fuzzy_day']);
				$('#add_previous_operation select[name="fuzzy_month"]').val(data['fuzzy_month']);
				$('#add_previous_operation select[name="fuzzy_year"]').val(data['fuzzy_year']);
				$('#add_previous_operation').slideToggle('fast');
				$('#btn-add_previous_operation').attr('disabled',true);
				$('#btn-add_previous_operation').addClass('disabled');
			}
		});

		e.preventDefault();
	});

	$('.removeOperation').live('click',function() {
		$('#operation_id').val($(this).attr('rel'));

		var removeOpDialog = $('#confirm_remove_operation_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560,
			autoOpen: false
		});
		removeOpDialog.dialog('open');

		return false;
	});


	$(document).on('click','.btn_cancel_remove_operation', function() {
		$(this).closest('.ui-dialog-content').dialog('close');
	});

	$(document).on('click','.btn_remove_operation', function() {
		$(this).closest('.ui-dialog-content').dialog('close');

		var opid = $(this).prev('#operation_id').val();
		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removePreviousOperation?patient_id=<?php echo $this->patient->id?>&operation_id='+opid,
			'success': function(html) {
				if (html == 'success') {
					$('a.removeOperation[rel="'+opid+'"]').parent().parent().remove();
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the operation.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the operation.\n\nPlease contact support for assistance."
				}).open();
			}
		});

		return false;
	});
</script>
