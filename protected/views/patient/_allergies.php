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
			Allergies
		</h3>
		<a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
				Show/hide this section
			</span>
		</a>
	</header>
	<div class="js-toggle-body">
		<?php
		if (!$this->patient->hasAllergyStatus()) {
			?>
			<p class="allergy-status">Patient allergy status is unknown</p>
			<?php
		} elseif ($this->patient->no_allergies_date) {
			?>
			<p class="allergy-status">Patient has no known allergies</p>
			<?php
		} else {
			?>
			<table class="plain patient-data" id="currentAllergies">
				<thead>
					<tr>
						<th>Allergies</th>
						<?php if ($this->checkAccess('OprnEditAllergy')) { ?><th>Actions</th><?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->patient->allergies as $allergy) { ?>
					<tr data-allergy-id="<?php echo $allergy->id ?>" data-allergy-name="<?php echo $allergy->name ?>">
						<td><?php echo $allergy->name ?></td>
						<?php if ($this->checkAccess('OprnEditAllergy')) { ?>
							<td>
								<a href="#" rel="<?php echo $allergy->id?>" class="small removeAllergy">
									Remove
								</a>
							</td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php
		}
		if ($this->checkAccess('OprnEditAllergy')) { ?>
			<div class="box-actions">
				<button id="btn-add_allergy" class="secondary small">
					Edit
				</button>
			</div>

			<div id="add_allergy" style="display: none;">
				<?php
				$form = $this->beginWidget('FormLayout', array(
					'id'=>'add-allergy',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array('class'=>'form add-data'),
					'action'=>array('patient/addAllergy'),
					'layoutColumns'=>array(
						'label' => 3,
						'field' => 9
					),
				))?>

				<div class="row field-row familyHistory">
					<div class="<?php echo $form->columns('label');?>">
						<label for="no_allergies">Confirm patient has no allergies:</label>
					</div>
					<div class="<?php echo $form->columns('field');?>">
						<?php echo CHtml::checkBox('no_allergies', $this->patient->no_allergies_date ? true : false); ?>
					</div>
				</div>

				<input type="hidden" name="edit_allergy_id" id="edit_allergy_id" value="" />
				<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

				<div class="row field-row familyHistory" id="allergy_field" <?php if ($this->patient->no_allergies_date) { echo 'style="display: none;"'; }?>>
					<div class="<?php echo $form->columns('label');?>">
						<label for="allergy_id">Add allergy:</label>
					</div>
					<div class="<?php echo $form->columns('field');?>">
						<?php echo CHtml::dropDownList('allergy_id', null, CHtml::listData($this->allergyList(), 'id', 'name'), array('empty' => '-- Select --'))?>
					</div>
				</div>

				<div class="buttons">
					<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_allergy_loader" style="display: none;" />
					<button class="secondary small btn_save_allergy" type="submit">Save</button>
					<button class="warning small btn_cancel_allergy" type="submit">Cancel</button>
				</div>

				<?php $this->endWidget()?>
			</div>
		<?php }?>
	</div>
</section>

<?php if (BaseController::checkAccess('OprnEditAllergy')) { ?>

	<!-- Confirm deletion dialog -->
	<div id="confirm_remove_allergy_dialog" title="Confirm remove allergy" style="display: none;">
		<div id="delete_allergy">
			<div class="alert-box alert with-icon">
				<strong>WARNING: This will remove the allergy from the patient record.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttons">
				<input type="hidden" id="remove_allergy_id" value="" />
				<button type="submit" class="warning small btn_remove_allergy">Remove allergy</button>
				<button type="submit" class="secondary small btn_cancel_remove_allergy">Cancel</button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('#no_allergies').bind('change', function() {
			if ($(this)[0].checked) {
				$('#allergy_field').hide().find('select').attr('disabled', 'disabled');
			}
			else {
				$('#allergy_field').show().find('select').removeAttr('disabled');
			}
		});

		$('#btn-add_allergy').click(function() {
			$('#relative_id').val('');
			$('#add_allergy').slideToggle('fast');
			$('#btn-add_allergy').attr('disabled',true);
			$('#btn-add_allergy').addClass('disabled');
		});
		$('button.btn_cancel_allergy').click(function() {
			$('#add_allergy').slideToggle('fast');
			$('#btn-add_allergy').attr('disabled',false);
			$('#btn-add_allergy').removeClass('disabled');
			return false;
		});
		$('button.btn_save_allergy').click(function() {
			if ($('#allergy_id').val() == '' && !$('#no_allergies')[0].checked) {
				new OpenEyes.UI.Dialog.Alert({
					content: "Please select an allergy or confirm patient has no allergies"
				}).open();
				return false;
			}
			$('img.add_allergy_loader').show();
			return true;
		});


		$('.removeAllergy').live('click',function() {
			$('#remove_allergy_id').val($(this).attr('rel'));

			$('#confirm_remove_allergy_dialog').dialog({
				resizable: false,
				modal: true,
				width: 560
			});

			return false;
		});

		$('button.btn_remove_allergy').click(function() {
			$("#confirm_remove_allergy_dialog").dialog("close");

			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/patient/removeAllergy?patient_id=<?php echo $this->patient->id?>&allergy_id='+$('#remove_allergy_id').val(),
				'success': function(html) {
					if (html == 'success') {
						var allergy_id = $('#remove_allergy_id').val();
						var row = $('#currentAllergies tr[data-allergy-id="' + allergy_id + '"]');
						var allergy_name = row.data('allergy-name');
						row.remove();
						$('#allergy_id').append('<option value="'+allergy_id+'">'+allergy_name+'</option>');
						sort_selectbox($('#allergy_id'));

					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, an internal error occurred and we were unable to remove the allergy.\n\nPlease contact support for assistance."
						}).open();
					}
				},
				'error': function() {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the allergy.\n\nPlease contact support for assistance."
					}).open();
				}
			});

			return false;
		});

		$('button.btn_cancel_remove_allergy').click(function() {
			$("#confirm_remove_allergy_dialog").dialog("close");
			return false;
		});
	</script>
<?php } ?>
