<?php
$this->breadcrumbs=array(
	'Phrase By Specialties',
);

$this->menu=array(
);
?>

<h1>Phrase By Specialties</h1>

<h2>Sections</h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_sectionview',
)); ?>
