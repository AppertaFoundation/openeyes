<?php
$this->breadcrumbs=array(
	'Sequences'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Sequence', 'url'=>array('index')),
	array('label'=>'Create Sequence', 'url'=>array('create')),
	array('label'=>'View Sequence', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Sequence', 'url'=>array('admin')),
);
?>

<h1>Update Sequence <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'firm'=>$firm)); ?>