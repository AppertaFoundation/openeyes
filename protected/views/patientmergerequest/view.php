<?php
/* @var $this PatientMergeRequestController */
/* @var $model PatientMergeRequest */

?>

<h1>View PatientMergeRequest #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'primary_id',
		'primary_hos_num',
		'primary_nhsnum',
		'primary_dob',
		'primary_gender',
		'secondary_id',
		'secondary_hos_num',
		'secondary_nhsnum',
		'secondary_dob',
		'secondary_gender',
		'merge_json',
		'comment',
		'status',
		'last_modified_user_id',
		'last_modified_date',
		'created_user_id',
		'created_date',
	),
)); ?>
