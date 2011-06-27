<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js'); ?>
<h3>Find a patient</h3>
<div id="patient_search">
<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'patient-search',
		'enableAjaxValidation'=>false,
		'action' => Yii::app()->createUrl('patient/search')
	));
	echo CHtml::label('Search by hospital number:', 'hospital_number');
	echo CHtml::textField('hospital_number');
	echo CHtml::submitButton('Find Patient');
	
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
	$this->endWidget(); ?>
</div>
<script type="text/javascript">
	$('input[id=hospital_number]').watermark('enter hospital number');
	$('input[id=first_name]').watermark('enter first name');
	$('input[id=last_name]').watermark('enter last name');
	$('input[id=nhs_number]').watermark('enter NHS number');
	$('input[id=dob_day]').watermark('DD');
	$('input[id=dob_month]').watermark('MM');
	$('input[id=dob_year]').watermark('YYYY');
</script>