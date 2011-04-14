<?php
$this->breadcrumbs=array(
	'Phrase By Specialties'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List PhraseBySpecialty', 'url'=>array('index')),
	array('label'=>'Create PhraseBySpecialty', 'url'=>array('create')),
	array('label'=>'Update PhraseBySpecialty', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete PhraseBySpecialty', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PhraseBySpecialty', 'url'=>array('admin')),
);
?>

<h1>View PhraseBySpecialty #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'phrase',
		'section_by_specialty_id',
		'display_order',
		'specialty_id',
	),
)); ?>
