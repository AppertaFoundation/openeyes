<?php
$this->breadcrumbs=array(
	'Global phrases' => array('/admin/phrase/index'), 
	$sectionName
);
$this->menu=array(
	array('label'=>'Create a phrase in this section', 'url'=> array('create', 'section_id'=>$sectionId)),
	array('label'=>'Manage phrases in this section', 'url'=>array('admin', 'section_id'=>$sectionId)),
);
?>

<h1>Global phrases</h1>
<h2>Phrases for the section: <?php echo $sectionName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view'
)); ?>
