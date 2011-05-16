<?php
$this->breadcrumbs=array(
	'Phrase By Specialties' => array('/admin/phraseBySpecialty/index'), 
	$sectionName => array('specialtyIndex', 'section_id'=>$sectionId),
	$specialtyName
);
$this->menu=array(
	array('label'=>'Create a phrase in section ' . $sectionName . ' for ' . $specialtyName . ' specialty', 'url'=> array('create', 'section_id'=>$sectionId)),
	array('label'=>'Manage phrases in this section', 'url'=>array('admin', 'section_id'=>$sectionId)),
);
?>

<h1>Phrase By Specialties</h1>
<h2>Phrases for the section: <?php echo $sectionName; ?> and the specialty: <?php echo $specialtyName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view'
)); ?>
