<?php
$this->breadcrumbs=array(
	'Common Ophthalmic Disorders'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List CommonOphthalmicDisorder', 'url'=>array('index')),
	array('label'=>'Create CommonOphthalmicDisorder', 'url'=>array('create')),
	array('label'=>'View CommonOphthalmicDisorder', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage CommonOphthalmicDisorder', 'url'=>array('admin')),
);
?>

<h1>Update CommonOphthalmicDisorder <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>