<section class="box patient-info associated-data js-toggle-container">
	<header class="box-header">
		<h3 class="box-title">
			<span class="icon-patient-clinician-hd_flag"></span>
			Family History
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
					<th>Relative</th>
					<th>Side</th>
					<th>Condition</th>
					<th>Comments</th>
					<?php if ($this->checkAccess('OprnEditFamilyHistory')) { ?><th>Actions</th><?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->patient->familyHistory as $history) {?>
					<tr>
						<td class="relative"><?php echo $history->relative->name?></td>
						<td class="side"><?php echo $history->side->name?></td>
						<td class="condition"><?php echo $history->condition->name?></td>
						<td class="comments"><?php echo CHtml::encode($history->comments)?></td>
						<?php if ($this->checkAccess('OprnEditFamilyHistory')): ?>
							<td>
								<a href="#" class="editFamilyHistory" rel="<?php echo $history->id?>">Edit</a>&nbsp;&nbsp;
								<a href="#" class="removeFamilyHistory" rel="<?php echo $history->id?>">Remove</a>
							</td>
						<?php endif ?>
					</tr>
				<?php }?>
			</tbody>
		</table>

		<?php if ($this->checkAccess('OprnEditFamilyHistory')) { ?>
			<div class="box-actions">
				<button id="btn-add_family_history" class="secondary small">
					Add Family History
				</button>
			</div>
			<div id="add_family_history" style="display: none;">

				<?php
				$form = $this->beginWidget('FormLayout', array(
					'id'=>'add-family_history',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array('class'=>'sliding form add-data'),
					'action'=>array('patient/addFamilyHistory'),
					'layoutColumns'=>array(
						'label' => 3,
						'field' => 9
					),
				))?>

					<fieldset class="field-row">

						<legend><strong>Add family history</strong></legend>

						<input type="hidden" name="edit_family_history_id" id="edit_family_history_id" value="" />
						<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="relative_id">Relative:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::dropDownList('relative_id','',CHtml::listData(FamilyHistoryRelative::model()->findAll(array('order'=>'display_order')),'id','name'),array('empty'=>'- Select -'))?>
							</div>
						</div>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="side_id">Side:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::dropDownList('side_id','',CHtml::listData(FamilyHistorySide::model()->findAll(array('order'=>'display_order')),'id','name'))?>
							</div>
						</div>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="condition_id">Condition:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::dropDownList('condition_id','',CHtml::listData(FamilyHistoryCondition::model()->findAll(array('order'=>'display_order')),'id','name'),array('empty'=>'- Select -'))?>
							</div>
						</div>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="comments">Comments:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::textField('comments','')?>
							</div>
						</div>

						<div class="buttons">
							<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_family_history_loader" style="display: none;" />
							<button type="submit" class="secondary small btn_save_family_history">
								Save
							</button>
							<button class="warning small btn_cancel_family_history">
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
<div id="confirm_remove_family_history_dialog" title="Confirm remove family history" style="display: none;">
	<div id="delete_family_history">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the family history from the patient record.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="family_history_id" value="" />
			<button type="submit" class="warning small btn_remove_family_history">Remove family history</button>
			<button type="submit" class="secondary small btn_cancel_remove_family_history">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#btn-add_family_history').click(function() {
		$('#relative_id').val('');
		$('div.familyHistory #side_id').val('');
		$('#condition_id').val('');
		$('#add_family_history #comments').val('');
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',true);
		$('#btn-add_family_history').addClass('disabled');
	});
	$('button.btn_cancel_family_history').click(function() {
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',false);
		$('#btn-add_family_history').removeClass('disabled');
		return false;
	});
	$('button.btn_save_family_history').click(function() {
		if ($('#relative_id').val() == '') {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please select a relative."
			}).open();
			return false;
		}
		if ($('#side_id').val() == '') {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please select a side."
			}).open();
			return false;
		}
		if ($('#condition_id').val() == '') {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please select a condition."
			}).open();
			return false;
		}
		$('img.add_family_history_loader').show();
		return true;
	});
	$('a.editFamilyHistory').click(function(e) {

		var tr = $(this).closest('tr');
		var history_id = $(this).attr('rel');

		$('#edit_family_history_id').val(history_id);
		var relative = tr.find('.relative').text();
		$('#relative_id').children('option').map(function() {
			if ($(this).text() == relative) {
				$(this).attr('selected','selected');
			}
		});
		var side = tr.find('.side').text();
		$('#side_id').children('option').map(function() {
			if ($(this).text() == side) {
				$(this).attr('selected','selected');
			}
		});
		var condition = tr.find('.condition').text();
		$('#condition_id').children('option').map(function() {
			if ($(this).text() == condition) {
				$(this).attr('selected','selected');
			}
		});
		$('#add_family_history #comments').val(tr.find('.comments').text());
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',true);
		$('#btn-add_family_history').addClass('disabled');

		e.preventDefault();
	});

	$('.removeFamilyHistory').live('click',function() {
		$('#family_history_id').val($(this).attr('rel'));

		$('#confirm_remove_family_history_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_family_history').click(function() {
		$("#confirm_remove_family_history_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removeFamilyHistory?patient_id=<?php echo $this->patient->id?>&family_history_id='+$('#family_history_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeFamilyHistory[rel="'+$('#family_history_id').val()+'"]').parent().parent().remove();
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the family_history.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the family_history.\n\nPlease contact support for assistance."
				}).open();
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_family_history').click(function() {
		$("#confirm_remove_family_history_dialog").dialog("close");
		return false;
	});
</script>
