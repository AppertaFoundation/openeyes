<?php
$patientName = $model->first_name . ' ' . $model->last_name;
$this->breadcrumbs=array(
	"{$patientName} ({$model->hos_num})",
);
Yii::app()->clientScript->registerCssFile('/css/patient.css', 'screen, projection');
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.fancybox',
	'config'=>array()
	));

?>
<div id="patientHeader">
	<strong>Patient:</strong> <?php echo $patientName;
	echo " ({$model->hos_num})"; ?>
</div>
<?php

if ($tab == 1) {
	$params = array('patient/episodes', 'id'=>$model->id);

	if (isset($eventId)) {
		$params['eventId'] = $eventId;
	}

	$this->widget('zii.widgets.jui.CJuiTabs', array(
	'tabs'=>array(
		'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
		'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id, 'event'=>$event)),
	),
	'id'=>'patient-tabs',
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>false,
		'selected'=>1
	),
));
} else {
	$this->widget('zii.widgets.jui.CJuiTabs', array(
		'tabs'=>array(
			'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
			'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id)),
		),
		'id'=>'patient-tabs',
		'themeUrl'=>Yii::app()->baseUrl . '/css/jqueryui',
		'theme'=>'theme',
		// additional javascript options for the tabs plugin
		'options'=>array(
			'collapsible'=>false,
		),
	));
}
