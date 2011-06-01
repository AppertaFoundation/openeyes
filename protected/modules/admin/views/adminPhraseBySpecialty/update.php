<?php
$this->breadcrumbs=array(
	'Phrase By Specialties'=>array('index'),
        $model->section->name => array('specialtyIndex', 'section_id'=>$model->section->id),
        $model->specialty->name => array('phraseIndex', 'specialty_id'=>$model->specialty->id, 'section_id'=>$model->section->id),
	$model->name->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List PhraseBySpecialty', 'url'=>array('index')),
	array('label'=>'Create PhraseBySpecialty', 'url'=>array('create')),
	array('label'=>'View PhraseBySpecialty', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage PhraseBySpecialty', 'url'=>array('admin')),
);
?>

<h1>Update PhraseBySpecialty <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
