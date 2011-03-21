<?php
$this->breadcrumbs=array(
	'Common Ophthalmic Disorders'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List CommonOphthalmicDisorder', 'url'=>array('index')),
	array('label'=>'Create CommonOphthalmicDisorder', 'url'=>array('create')),
	array('label'=>'Update CommonOphthalmicDisorder', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete CommonOphthalmicDisorder', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage CommonOphthalmicDisorder', 'url'=>array('admin')),
);
?>

<h1>View CommonOphthalmicDisorder #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name' => 'disorder',
			'value' => CHtml::encode($model->disorder->term)
		),
		array(
			'name' => 'specialty_id',
			'value' => CHtml::encode($model->specialty->name)
		),
	),
)); ?>
