<?php
$this->breadcrumbs=array(
	'Phrase By Specialties'=>array('index'),
	$model->section->name => array('specialtyIndex', 'section_id'=>$model->section->id),
	$specialtyName => array('phraseIndex', 'specialty_id'=>$_GET['specialty_id'], 'section_id'=>$_GET['section_id']),
	$model->name->name,
);

$this->menu=array(
	array('label'=>'Update this phrase', 'url'=>array('update', 'id'=>$model->id,'section_id'=>$_GET['section_id'],'specialty_id'=>$_GET['specialty_id'])),
	array('label'=>'Delete this phrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'List all phrases for this section and specialty', 'url'=>array('phraseindex', 'specialty_id'=>$_GET['specialty_id'],'section_id'=>$model->section->id)),
);
?>

<h1>View PhraseBySpecialty #<?php echo $model->id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array('name' => 'name', 'value' => $model->name->name),
		'phrase',
		array('name' => 'section_id', 'value' => $model->section->name),
		'display_order',
		array('name' => 'specialty_id', 'value' => $model->specialty->name),
	),
)); ?>
