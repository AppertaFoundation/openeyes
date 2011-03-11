<?php
$this->breadcrumbs=array(
	'Diagnosises'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Diagnoses', 'url'=>array('index'))
);
?>

<h1>Create Diagnosis</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>