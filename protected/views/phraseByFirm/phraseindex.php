<?php
$this->breadcrumbs=array(
	'Phrase By Firm' => array('/phraseByFirm/index'), 
	$sectionName
);
$this->menu=array(
	array('label'=>'Create a phrase in this section', 'url'=> array('create', 'section_id'=>$sectionId)),
);
?>

<h1>Phrase By Firm</h1>
<h2>Phrases for the section: <?php echo $sectionName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view'
)); ?>
