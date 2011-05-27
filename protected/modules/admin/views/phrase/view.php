<?php
$this->breadcrumbs=array(
	'Global Phrases'=>array('index'),
	$model->section->name => array('phraseindex', 'section_id'=>$model->section->id),
	$model->name->name,
);

$this->menu=array(
	array('label'=>'Update this phrase', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete this phrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'List all phrases in this section', 'url'=>array('phraseindex', 'section_id'=>$model->section->id)),
);
?>

<h1>View global phrase: #<?php echo $model->id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'phrase',
		array('name' => 'section_id', 'value' => $model->section->name),
		'display_order',
	),
)); ?>
