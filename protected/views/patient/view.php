<?php
$patientName = $model->first_name . ' ' . $model->last_name;
$this->breadcrumbs=array(
	"{$patientName} ({$model->hos_num})",
);
?>
<div id="patientHeader">
	<strong>Patient:</strong> <?php echo $patientName;
	echo " ({$model->hos_num})"; ?>
</div>
<?php
$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array(
		'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
		'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id)),
		'Contacts'=>array('ajax'=>array('patient/contacts', 'id'=>$model->id)),
		'Correspondence'=>array('ajax'=>array('patient/correspondence', 'id'=>$model->id)),
	),
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>false,
    ),
)); ?>