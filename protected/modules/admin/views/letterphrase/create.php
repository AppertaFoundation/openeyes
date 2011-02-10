<?php
$this->breadcrumbs=array(
	'Letterphrases'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Letterphrase', 'url'=>array('index')),
	array('label'=>'Manage Letterphrase', 'url'=>array('admin')),
);
?>

<h1>Create Letterphrase</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>