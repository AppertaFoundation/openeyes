<?php
$this->breadcrumbs=array(
	'Letter Templates'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Letter Template', 'url'=>array('index')),
	array('label'=>'Create Letter Template', 'url'=>array('create')),
	array('label'=>'Update Letter Template', 'url'=>array('update', 'id'=>$model->id)),
);
?>

<h1>View Letter Template #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'phrase',
                array(
                        'name' => 'specialty_id',
                        'value' => CHtml::encode($model->getSpecialtyText())
                ),
                array(
                        'name' => 'to',
                        'value' => CHtml::encode($model->getToText())
                ),
                array(
                        'name' => 'cc',
                        'value' => CHtml::encode($model->getCcText())
                ),
	),
)); ?>
