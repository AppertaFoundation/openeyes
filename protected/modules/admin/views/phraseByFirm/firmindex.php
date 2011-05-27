<?php
$this->breadcrumbs=array(
	'Phrase By Firm' => array('/admin/phraseByFirm/index'), 
	$sectionName
);
$this->menu=array(
);
?>

<h1>Phrase By Firm</h1>
<h2>List of firms in section: <?php echo $sectionName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_firmview'
)); ?>
