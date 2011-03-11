<?php
$this->breadcrumbs=array(
	'Diagnoses',
);

$this->menu=array(
	array('label'=>'Create Diagnosis', 'url'=>array('create'))
);
?>

<h1>Diagnoses</h1>

<?php

foreach($diagnoses as $diagnosis) {
?>
Disorder: <?php echo $diagnosis->disorder->term ?><br />
Location: <?php echo $diagnosis->getLocationText() ?><br />
User: <?php echo $diagnosis->user->first_name ?> <?php echo $diagnosis->user->last_name ?><br />
Created on: <?php echo $diagnosis->created_on ?><br />

<?php

	if ($diagnosis->user_id == Yii::app()->user->id) {
		echo CHtml::link(
			'delete',
			Yii::app()->createUrl('diagnosis/delete', array(
				'id' => $diagnosis->id
			))
		);
	}
?>
<br /><br />
<?php
}
