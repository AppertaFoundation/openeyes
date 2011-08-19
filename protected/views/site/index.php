<?php

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');
$cs->registerScriptFile($baseUrl.'/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');

$this->layout = 'main'; ?>
<div class="text">Find a patient:</div>
<div id="patient-search-error" class="rounded-corners">
No patients found.
</div>
<div id="patient_search">
<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'patient-search',
		'enableAjaxValidation'=>true,
		//'action' => Yii::app()->createUrl('patient/results')
	)); ?>
	<div class="title_bar"><?php
	echo CHtml::label('Search by hospital number:', 'hospital_number');
	echo CHtml::textField('Patient[hos_num]', '', array('style'=>'width: 204px;'));
?>
<button type="submit" value="submit" class="shinybutton highlighted" id="findPatient"><span>Find patient</span></button>
<?php
//	echo CHtml::submitButton('Find Patient'); ?></div><?php
	
	$this->widget('zii.widgets.jui.CJuiAccordion', array(
		'panels'=>array(
			'or search using patient details'=>$this->renderPartial('/patient/_advanced_search',
				array(),true),
		),
		// additional javascript options for the accordion plugin
		'options'=>array(
			'active'=>false,
			'animated'=>'bounceslide',
			'collapsible'=>true,
		),
	));
	$this->endWidget();
?>
</div>
<script type="text/javascript">
	$('#findPatient').click(function() {
		if (!$('#Patient_hos_num').val() && !$('#Patient_last_name').val()) {
			$('#patient-search-error').html('Please enter either a hospital number or a surname.');
			$('#patient-search-error').show();
			$('#patient-list').hide();
			return false;
		}

		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('patient/results'); ?>',
			'type': 'POST',
			'data': $('#patient-search').serialize(),
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
							// Nultiple results, populate list

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
						$('#patient-search-error').html('There are no patients with those details.');
						$('#patient-search-error').show();
						$('#patient-list').hide();
					}
				} catch (e) {
					// @todo - this can be indicative of a login timeout, redirect perhaps?
				}
			}
		});
		return false;
	});
</script>
<div id="patient-list" class="rounded-corners">
</div>
<div id="sidebox">
	<h3>Do you need help with OpenEyes?</h3>
	<strong>Before you contact the helpdesk...</strong><br />
	<ul>
		<li>Is there a "Super User" in your office available?</li>
		<li>Have you checked the <a href="#">Quick Reference Guide</a>?</li>
	</ul>
	<em>Still need help?</em> Contact us:
	<div class="blue_highlight">
		Telephone: <span class="number">ext. 0000</span><br />
		Email: <span class="number">helpdesk@openeyes.org.uk</span>
	</div>
</div>
<script type="text/javascript">
	$.watermark.options = {
		className: 'watermark',
		useNative: false
	};
	$('input[id=hospital_number]').watermark('enter hospital number');
	$('input[id=first_name]').watermark('enter first name');
	$('input[id=last_name]').watermark('enter last name');
	$('input[id=nhs_number]').watermark('enter NHS number');
	$('input[id=dob_day]').watermark('DD');
	$('input[id=dob_month]').watermark('MM');
	$('input[id=dob_year]').watermark('YYYY');
</script>
