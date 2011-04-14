<?php
$this->breadcrumbs=array(
	'Phrase By Specialties'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List PhraseBySpecialty', 'url'=>array('index')),
	array('label'=>'Manage PhraseBySpecialty', 'url'=>array('admin')),
);
?>

<h1>Create PhraseBySpecialty</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>