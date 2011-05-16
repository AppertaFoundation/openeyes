<?php
$this->breadcrumbs=array(
	'Phrase By Specialties' => array('/admin/phraseBySpecialty/index'), 
	$sectionName
);
$this->menu=array(
);
?>

<h1>Phrase By Specialties</h1>
<h2>Specialties for the section: <?php echo $sectionName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_specialtyview'
)); ?>
