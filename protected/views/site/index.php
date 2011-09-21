<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');
$cs->registerScriptFile($baseUrl.'/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');

$this->layout = 'main'; ?>
<h2>Patient search</h2>
<div class="centralColumn">
	<p><strong>Find a patient.</strong> Either by hospital number or by personal details. You must know their surname.</p>
	<div id="patient-search-error" class="rounded-corners">
		No patients found.
	</div>
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'patient-search',
		'enableAjaxValidation'=>true,
		'focus'=>'#Patient_hos_num',
		//'action' => Yii::app()->createUrl('patient/results')
	));?>
	<div id="search_patient_id" class="form_greyBox bigInput">
		<?php
			echo CHtml::label('Search by hospital number:', 'hospital_number');
			echo CHtml::textField('Patient[hos_num]', '', array('style'=>'width: 204px;'));
		?>
		<button type="submit" value="submit" class="btn_findPatient ir" id="findPatient_id">Find patient</button>
		<?php $this->endWidget();?>
	</div>
	<?php
	$this->widget('zii.widgets.jui.CJuiAccordion', array(
		'id' => 'patient-adv-search',
		'panels'=>array(
			'or search using patient details'=>$this->renderPartial('/patient/_advanced_search',
				array(),true),
		),
		// additional javascript options for the accordion plugin
		'options'=>array(
			'active'=>0,
			'animated'=>false,
			'collapsible'=>true,
		),
	));
	?>
	</form>
</div><!-- .centralColumn -->
<div id="search-form" class="">
</div><!-- search-form -->
<script type="text/javascript">
	$('#findPatient_id').click(function() {
		if (!$('#Patient_hos_num').val()) {
			$('#patient-search-error').html('Please enter a hospital number.');
			$('#patient-search-error').show();
			$('#patient-list').hide();
			return false;
		}
		patient_search($('#patient-search').serialize());
	});

	$('#findPatient_details').click(function() {
		if (!$('#Patient_last_name').val() || !$('#Patient_first_name').val()) {
			$('#patient-search-error').html('Please enter at least a first name and surname.');
			$('#patient-search-error').show();
			$('#patient-list').hide();
			return false;
		}
		patient_search($('#patient-adv-search-form').serialize());
	});

	function patient_search(postdata) {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('patient/results'); ?>',
			'type': 'POST',
			'data': postdata,
			'success': function(data) {
				try {
					arr = $.parseJSON(data);

					patientViewUrl = '<?php echo Yii::app()->createUrl('patient/view', array('id' => 'patientId')) ?>';

					if (!$.isEmptyObject(arr)) {
						if (arr.length == 1) {
							// One result, forward to the patient summary page
							patientViewUrl = patientViewUrl.replace(/patientId/, arr[0]['id']);

							window.location.replace(patientViewUrl);
						} else if (arr.length > 1) {
							// Multiple results, populate list
							if ($('#patient-adv-search div.ui-accordion-content:visible').length > 0) {
								// hide advanced search options, if visible
								$('#patient-adv-search').accordion('activate', 0);
							}

							content = '<br /><strong>' + arr.length + " results found</strong><p />\n";
							content += "<table><tr><th>Patient name</th><th>Date of Birth</th><th>Gender</th><th>NHS Number</th><th>Hospital Number</th></tr>\n";

							$.each(arr, function(index, value) {
								if (value['gender'] == 'M') {
									gender = 'Male';
								} else {
									gender = 'Female';
								}
								content += '<tr><td>' + value['first_name'] + ' ' + value['last_name'] + '</td><td>' + value['dob'] + '</td><td>' + gender;
								content += '</td><td>' + value['nhs_num'] + '</td><td>';
								content += '<a href="index.php?r=patient/view&id=' + value['id'];
								content += '">' + value['hos_num'] + "</a></td></tr>\n";
							});

							content += "</table>\n";

							$('#patient-list').html(content);

							$('#patient-search-error').hide();
							$('#patient-list').show();
						}
					} else {
						$('#patient-search-error').html('No patients found matching the selected options. Please choose different options and try again.');
						$('#patient-search-error').show();
						$('#patient-list').hide();
					}
				} catch (e) {
				}
			}
		});
		return false;
	}
</script>
