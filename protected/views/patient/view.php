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
// @todo: figure out why 'selected'=>$tab breaks the display b/c Yii is stupid
if ($tab == 1) {
	$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array(
		'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
		'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id)),
		'Contacts'=>array('ajax'=>array('patient/contacts', 'id'=>$model->id)),
		'Correspondence'=>array('ajax'=>array('patient/correspondence', 'id'=>$model->id)),
	),
	'id'=>'patient-tabs',
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>false,
		'selected'=>1 // if we replace 1 with $tab, it breaks...WTF??
    ),
));
} else {
	$this->widget('zii.widgets.jui.CJuiTabs', array(
		'tabs'=>array(
			'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
			'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id)),
			'Contacts'=>array('ajax'=>array('patient/contacts', 'id'=>$model->id)),
			'Correspondence'=>array('ajax'=>array('patient/correspondence', 'id'=>$model->id)),
		),
		'id'=>'patient-tabs',
		'themeUrl'=>Yii::app()->baseUrl . '/css/jqueryui',
		'theme'=>'theme',
		// additional javascript options for the tabs plugin
		'options'=>array(
			'collapsible'=>false,
		),
	));
}?>