<?php
$this->breadcrumbs=array(
	'Global phrases',
);

$this->menu=array(
);
?>

<h1>Global phrases</h1>

<h2>Sections</h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_sectionview',
)); ?>
