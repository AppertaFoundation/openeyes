<?php
$this->breadcrumbs=array(
	'Patients'=>array('index'),
	'Search',
);
?>

<h1>Search Patients</h1>

<p>
Enter as many fields as you would like for searching.
</p>

<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>