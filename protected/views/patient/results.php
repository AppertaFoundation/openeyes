<?php
$this->breadcrumbs=array(
	'Patients'=>array('index'),
	'Search',
);
?>

<h1>Search Results</h1>
<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_view',
    'template'=>"{items}\n{pager}",
)); ?>