<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'Update User', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
	array('label'=>'User Rights', 'url'=>array('rights', 'id'=>$model->id)),
);
?>

<h1>View User #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'username',
		'first_name',
		'last_name',
		'email',
		array(
			'name' => 'active',
			'value' => CHtml::encode($model->getActiveText())
		),
                array(
                        'name' => 'global_firm_rights',
                        'value' => CHtml::encode($model->getGlobalFirmRightsText())
                ),
	),
)); ?>
