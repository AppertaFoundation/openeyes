<?php
$this->breadcrumbs=array(
	'Examphrases'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Examphrase', 'url'=>array('index')),
	array('label'=>'Manage Examphrase', 'url'=>array('admin')),
);
?>

<h1>Create Examphrase</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>