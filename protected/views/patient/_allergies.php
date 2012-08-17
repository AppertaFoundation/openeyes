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
	</div>
	<div class="data_row" id="add_new_allergy">
		Add Allergy:
		<?php echo CHtml::dropDownList('allergy_id', null, CHtml::listData($this->allergyList(), 'id', 'name'), array('empty' => '-- Select --'));?>
		<button id="btn_add_allergy" class="classy green mini" type="button"><span class="button-span button-span-green">Add</span></button>
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
		var option = $('#allergy_id option:selected').first();
		$.post("<?php echo Yii::app()->createUrl('patient/AddAllergy')?>", { patient_id: patient_id, allergy_id: allergy_id }, function(data) {
			var new_row = $('<tr data-allergy-id="'+allergy_id+'"></tr>');
			new_row.append($('<td>'+option.text()+'</td>'), $('<td><a href="#" class="small removeAllergy"><strong>Remove</strong></a></td>'));
			$('#patient_allergies tbody').append(new_row);
			option.attr('disabled','disabled');
		});
		$('#allergy_id').val('');
		return false;
	});
	
	// Remove allergy
	$('#patient_allergies').delegate('a.removeAllergy', 'click', function() {
		var row = $(this).closest('tr');
		var allergy_id = row.attr('data-allergy-id');
		var patient_id = <?php echo $this->patient->id; ?>;
		$.post("<?php echo Yii::app()->createUrl('patient/RemoveAllergy')?>", { patient_id: patient_id, allergy_id: allergy_id }, function(data) {
			row.remove();
			$('#allergy_id option[value="' + allergy_id + '"]').removeAttr('disabled');
		});
		return false;
	});
	
</script>
