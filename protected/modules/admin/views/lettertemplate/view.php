<?php
$this->breadcrumbs=array(
	'Lettertemplates'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Lettertemplate', 'url'=>array('index')),
	array('label'=>'Create Lettertemplate', 'url'=>array('create')),
	array('label'=>'Update Lettertemplate', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Lettertemplate', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Lettertemplate', 'url'=>array('admin')),
);
?>

<h1>View Lettertemplate #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name' => 'specialty_id',
			'value' => CHtml::encode($model->specialty->name)
		),
		'name',
		array(
			'name' => 'contact_type_id',
			'value' => CHtml::encode($model->contactType->name)
		),
		'text',
		'cc',
	),
)); ?>
