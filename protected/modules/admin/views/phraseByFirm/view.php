<?php
$this->breadcrumbs=array(
	'Phrase By Firms'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List PhraseByFirm', 'url'=>array('index')),
	array('label'=>'Create PhraseByFirm', 'url'=>array('create')),
	array('label'=>'Update PhraseByFirm', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete PhraseByFirm', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PhraseByFirm', 'url'=>array('admin')),
);
?>

<h1>View PhraseByFirm #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'phrase',
		'section_by_firm_id',
		'display_order',
		'firm_id',
	),
)); ?>
