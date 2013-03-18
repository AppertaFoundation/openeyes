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
					<th>Edit</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->patient->allergies as $allergy) { ?>
				<tr data-allergy-id="<?php echo $allergy->id ?>">
					<td><?php echo $allergy->name ?></td>
					<td><a href="#" class="small removeAllergy"><strong>Remove</strong>
					</a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<div id="add_new_allergy">
			Add Allergy:
			<?php echo CHtml::dropDownList('allergy_id', null, CHtml::listData($this->allergyList(), 'id', 'name'), array('empty' => '-- Select --'));?>
			<button id="btn_add_allergy" class="classy green mini" type="button"><span class="button-span button-span-green">Add</span></button>
		</div>
	</div>
</div>
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
				<input type="hidden" id="allergy_id" value="" />
				<button type="submit" class="classy red venti btn_remove_allergy"><span class="button-span button-span-red">Remove allergy</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_allergy"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<!-- #patient_allergies -->
<script type="text/javascript">

	var patient_id = <?php echo $this->patient->id; ?>;

	// Disable current allergies in dropdown
	$('#patient_allergies tr').each(function(index) {
		var allergy_id = $(this).attr('data-allergy-id');
		var option = $('#allergy_id option[value="' + allergy_id + '"]');
		if(option) {
			option.attr('disabled','disabled');
		}
	});
	
	// Add allergy
	$('body').delegate('#btn_add_allergy','click', function() {
		var allergy_id = $('#allergy_id').val();
		if(allergy_id) {
			var option = $('#allergy_id option:selected').first();
			$.post("<?php echo Yii::app()->createUrl('patient/AddAllergy')?>", { patient_id: patient_id, allergy_id: allergy_id }, function(data) {
				var new_row = $('<tr data-allergy-id="'+allergy_id+'"></tr>');
				new_row.append($('<td>'+option.text()+'</td>'), $('<td><a href="#" class="small removeAllergy"><strong>Remove</strong></a></td>'));
				$('#patient_allergies tbody').append(new_row);
				option.attr('disabled','disabled');
			});
		}
		$('#allergy_id').val('');
		return false;
	});
	
	// Remove allergy
	$('#patient_allergies').delegate('a.removeAllergy', 'click', function() {
		$('#allergy_id').val($(this).closest('tr').attr('data-allergy-id'));

		$('#confirm_remove_allergy_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_allergy').click(function() {
		$("#confirm_remove_allergy_dialog").dialog("close");

		$.post("<?php echo Yii::app()->createUrl('patient/RemoveAllergy')?>", { patient_id: <?php echo $this->patient->id?>, allergy_id: $('#allergy_id').val() }, function(data) {
			$('tr[data-allergy-id="'+$('#allergy_id').val()+'"]').remove();
			$('#allergy_id option[value="' + $('#allergy_id').val() + '"]').removeAttr('disabled');
			$('#allergy_id').val('');
		});

		return false;
	});

	$('button.btn_cancel_remove_allergy').click(function() {
		$('#allergy_id').val('');
		$("#confirm_remove_allergy_dialog").dialog("close");
		return false;
	});
</script>
