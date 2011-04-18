<?php
$this->breadcrumbs=array(
	'Phrase By Specialties'=>array('index'),
	$model->sectionBySpecialty->name => array('phraseindex', 'section_id'=>$model->sectionBySpecialty->id),
	$model->name,
);

$this->menu=array(
	array('label'=>'Update this phrase', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete this phrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'List all phrases in this section', 'url'=>array('phraseindex', 'section_id'=>$model->sectionBySpecialty->id)),
	array('label'=>'Create new phrase in this section', 'url'=>array('create', 'section_id'=>$model->sectionBySpecialty->id)),
	array('label'=>'Manage phrases', 'url'=>array('admin')),
);
?>

<h1>View PhraseBySpecialty #<?php echo $model->id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'phrase',
		array('name' => 'section_by_specialty_id', 'value' => $model->sectionBySpecialty->name),
		'display_order',
		array('name' => 'specialty_id', 'value' => $model->specialty->name),
	),
)); ?>
