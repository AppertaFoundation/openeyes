<?php
$this->breadcrumbs=array(
	'Sequences'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Sequence', 'url'=>array('index')),
	array('label'=>'Manage Sequence', 'url'=>array('admin')),
);
?>

<h1>Create Sequence</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'firm'=>$firm)); ?>