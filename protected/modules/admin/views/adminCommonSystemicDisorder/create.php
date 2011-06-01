<?php
$this->breadcrumbs=array(
	'Common Systemic Disorders'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List CommonSystemicDisorder', 'url'=>array('index')),
	array('label'=>'Manage CommonSystemicDisorder', 'url'=>array('admin')),
);
?>

<h1>Create CommonSystemicDisorder</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>