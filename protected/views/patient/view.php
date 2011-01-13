<?php
$this->breadcrumbs=array(
	'Patients'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List Patient', 'url'=>array('index')),
	array('label'=>'Create Patient', 'url'=>array('create')),
	array('label'=>'Update Patient', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Patient', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Patient', 'url'=>array('admin')),
);
?>

<h1>View Patient #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'pas_key',
		'title',
		'first_name',
		'last_name',
		'dob',
		'gender',
		'hos_num',
		'nhs_num',
		'address1',
		'address2',
		'city',
		'postcode',
		'country',
		'telephone',
		'mobile',
		'email',
		'comments',
		'pmh',
		'poh',
		'drugs',
		'allergies',
	),
)); ?>
