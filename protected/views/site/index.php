<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');
$this->layout = 'main'; ?>
<div class="text">Find a patient:</div>
<div id="patient_search">
<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'patient-search',
		'enableAjaxValidation'=>false,
		'action' => Yii::app()->createUrl('patient/search')
	)); ?>
	<div class="title_bar"><?php
	echo CHtml::label('Search by hospital number:', 'hospital_number');
	echo CHtml::textField('hospital_number', '', array('style'=>'width: 204px;'));
	echo CHtml::submitButton('Find Patient'); ?></div><?php
	
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