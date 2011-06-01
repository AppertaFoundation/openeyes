<?php
$this->breadcrumbs=array(
	'Phrase By Firm',
);

$this->menu=array(
);
?>

<h1>Phrase By Firm</h1>

<h2>Sections</h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_sectionview',
)); ?>
