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
<div class="whiteBox forClinicians" id="patient_allergies">
	<div class="patient_actions">
		<span class="aBtn"><a class="sprite showhide" href="#"><span
				class="hide"></span> </a> </span>
	</div>
	<div class="icon_patientIssue"></div>
	<h4>Allergies</h4>
	<div class="data_row">
		<table class="subtleWhite">
			<thead>
				<tr>
					<th width="80%">Allergies</th>
					<?php if(BaseController::checkUserLevel(3)) { ?><th>Edit</th><?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->patient->allergies as $allergy) { ?>
				<tr data-allergy-id="<?php echo $allergy->id ?>">
					<td><?php echo $allergy->name ?></td>
					<?php if(BaseController::checkUserLevel(3)) { ?>
					<td><a href="#" rel="<?php echo $allergy->id?>" class="small removeAllergy"><strong>Remove</strong>
					<?php } ?>
					</a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<?php if(BaseController::checkUserLevel(3)) { ?>
			<div align="center" style="margin-top:10px;">
				<form><button id="btn-add_allergy" class="classy green mini" type="button"><span class="button-span button-span-green">Add allergy</span></button></form>
			</div>
			<div id="add_allergy" style="display: none;">
				<h5>Add allergy</h5>
				<?php
				$form = $this->beginWidget('CActiveForm', array(
						'id'=>'add-allergy',
						'enableAjaxValidation'=>false,
						'htmlOptions' => array('class'=>'sliding'),
						'action'=>array('patient/addAllergy'),
				))?>

				<input type="hidden" name="edit_allergy_id" id="edit_allergy_id" value="" />
				<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

				<div class="familyHistory">
					<div class="label">
						Allergy:
					</div>
					<div class="data">
						<?php echo CHtml::dropDownList('allergy_id', null, CHtml::listData($this->allergyList(), 'id', 'name'), array('empty' => '-- Select --'))?>
					</div>
				</div>

				<div align="right">
					<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_allergy_loader" style="display: none;" />
					<button class="classy green mini btn_save_allergy" type="submit"><span class="button-span button-span-green">Save</span></button>
					<button class="classy red mini btn_cancel_allergy" type="submit"><span class="button-span button-span-red">Cancel</span></button>
				</div>

				<?php $this->endWidget()?>
			</div>
		<?php }?>
	</div>
</div>
<?php if(BaseController::checkUserLevel(3)) { ?>
<div id="confirm_remove_allergy_dialog" title="Confirm remove allergy" style="display: none;">
	<div>
		<div id="delete_allergy">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the allergy from the patient record.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="remove_allergy_id" value="" />
				<button type="submit" class="classy red venti btn_remove_allergy"><span class="button-span button-span-red">Remove allergy</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_allergy"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<!-- #patient_allergies -->
<script type="text/javascript">
	$('#btn-add_allergy').click(function() {
		$('#relative_id').val('');
		$('div.familyHistory #side_id').val('');
		$('#condition_id').val('');
		$('div.familyHistory #comments').val('');
		$('#add_allergy').slideToggle('fast');
		$('#btn-add_allergy').attr('disabled',true);
		$('#btn-add_allergy').removeClass('green').addClass('disabled');
		$('#btn-add_allergy span').removeClass('button-span-green').addClass('button-span-disabled');
	});
	$('button.btn_cancel_allergy').click(function() {
		$('#add_allergy').slideToggle('fast');
		$('#btn-add_allergy').attr('disabled',false);
		$('#btn-add_allergy').removeClass('disabled').addClass('green');
		$('#btn-add_allergy span').removeClass('button-span-disabled').addClass('button-span-green');
		return false;
	});
	$('button.btn_save_allergy').click(function() {
		if ($('#allergy_id').val() == '') {
			alert("Please select an allergy");
			return false;
		}
		$('img.add_allergy_loader').show();
		return true;
	});
	$('a.editAllergy').click(function(e) {
		var history_id = $(this).attr('rel');

		$('#edit_allergy_id').val(history_id);
		var relative = $(this).parent().parent().children('td:first').text();
		$('#relative_id').children('option').map(function() {
			if ($(this).text() == relative) {
				$(this).attr('selected','selected');
			}
		});
		var side = $(this).parent().parent().children('td:nth-child(2)').text();
		$('#side_id').children('option').map(function() {
			if ($(this).text() == side) {
				$(this).attr('selected','selected');
			}
		});
		var condition = $(this).parent().parent().children('td:nth-child(3)').text();
		$('#condition_id').children('option').map(function() {
			if ($(this).text() == condition) {
				$(this).attr('selected','selected');
			}
		});
		$('div.familyHistory #comments').val($(this).parent().prev('td').text());
		$('#add_allergy').slideToggle('fast');
		$('#btn-add_allergy').attr('disabled',true);
		$('#btn-add_allergy').removeClass('green').addClass('disabled');
		$('#btn-add_allergy span').removeClass('button-span-green').addClass('button-span-disabled');

		e.preventDefault();
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
					$('a.removeAllergy[rel="'+$('#remove_allergy_id').val()+'"]').parent().parent().remove();
				} else {
					alert("Sorry, an internal error occurred and we were unable to remove the allergy.\n\nPlease contact support for assistance.");
				}
			},
			'error': function() {
				alert("Sorry, an internal error occurred and we were unable to remove the allergy.\n\nPlease contact support for assistance.");
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
