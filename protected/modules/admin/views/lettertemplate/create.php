<?php
$this->breadcrumbs=array(
	'Lettertemplates'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Lettertemplate', 'url'=>array('index')),
	array('label'=>'Manage Lettertemplate', 'url'=>array('admin')),
);
?>

<h1>Create Lettertemplate</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>