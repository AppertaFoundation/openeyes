<?php
$this->breadcrumbs=array(
	'Phrase By Firm' => array('/admin/phraseByFirm/index'), 
	$sectionName => array('firmIndex', 'section_id'=>$sectionId),
	$firmName
);
$this->menu=array(
	array('label'=>'Create a phrase for this firm', 'url'=> array('create', 'section_id'=>$sectionId, 'firm_id'=>$firmId)),
	array('label'=>'Manage phrases for this firm', 'url'=>array('admin', 'section_id'=>$sectionId)),
);
?>

<h1>Phrase By Firm</h1>
<h2>Phrases for the section: <?php echo $sectionName; ?> and the firm: <?php echo $firmName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view'
)); ?>
