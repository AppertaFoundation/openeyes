<?php
$this->breadcrumbs=array(
	'Examphrases'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Examphrase', 'url'=>array('index')),
	array('label'=>'Create Examphrase', 'url'=>array('create')),
	array('label'=>'Update Examphrase', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Examphrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Examphrase', 'url'=>array('admin')),
);
?>

<h1>View Examphrase #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name' => 'specialty_id',
			'value' => CHtml::encode($model->specialty->name)
		),
		array(
			'name' => 'part',
			'value' => CHtml::encode($model->getPartText())
		),
		'phrase',
		'display_order',
	),
)); ?>
