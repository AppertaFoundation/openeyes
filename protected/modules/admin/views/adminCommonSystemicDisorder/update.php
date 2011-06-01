<?php
$this->breadcrumbs=array(
	'Common Systemic Disorders'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List CommonSystemicDisorder', 'url'=>array('index')),
	array('label'=>'Create CommonSystemicDisorder', 'url'=>array('create')),
	array('label'=>'View CommonSystemicDisorder', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage CommonSystemicDisorder', 'url'=>array('admin')),
);
?>

<h1>Update CommonSystemicDisorder <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>