<?php
$this->breadcrumbs=array(
	'Common Ophthalmic Disorders'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List CommonOphthalmicDisorder', 'url'=>array('index')),
	array('label'=>'Manage CommonOphthalmicDisorder', 'url'=>array('admin')),
);
?>

<h1>Create CommonOphthalmicDisorder</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>