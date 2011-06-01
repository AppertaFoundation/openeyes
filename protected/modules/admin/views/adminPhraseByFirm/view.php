<?php
$this->breadcrumbs=array(
        'Phrase By Firm' => array('/admin/phraseByFirm/index'),
        $model->section->name => array('firmIndex', 'section_id'=>$model->section->id),
        $model->firm->name => array('phraseIndex', 'section_id'=>$model->section->id, 'firm_id'=>$model->firm->id),
	$model->name->name,
);

$this->menu=array(
	array('label'=>'Update this phrase', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete this phrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id, 'section_id'=>$model->section->id, 'firm_id'=>$model->firm->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'List all phrases for this section and firm', 'url'=>array('phraseindex', 'firm_id'=>$model->firm->id,'section_id'=>$model->section->id)),
);
?>

<h1>View PhraseByFirm #<?php echo $model->id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array('name' => 'phrase_name_id', 'value' => $model->name->name),
		'phrase',
		array('name' => 'section_by_firm_id', 'value' => $model->section->name),
		'display_order',
		array('name' => 'firm_id', 'value' => $model->firm->name),
	),
)); ?>
