<?php
$this->breadcrumbs=array(
	'Event Types'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List EventType', 'url'=>array('index')),
	array('label'=>'Manage EventType', 'url'=>array('admin')),
);
?>

<h1>Create EventType</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>