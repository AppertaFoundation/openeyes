<h3>Summary</h3>
<div class="details">
	<h3>Personal Details</h3>
	<div class="data_row">
		<div class="data_label">First name(s):</div>
		<div class="data_value"><?php echo $model->first_name; ?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Last name:</div>
		<div class="data_value"><?php echo $model->last_name; ?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Address:</div>
		<div class="data_value">
<?php 
	if (!empty($address)) {
		echo $address->address1;
		echo '<br />';
		echo $address->address2;
		echo '<br />';
		echo $address->city;
		echo '<br />';
		echo $address->county;
		echo '<br />';
		echo $address->postcode;
	} else {
		echo 'Unknonwn';
	} ?>
		</div>
	</div>
	<div class="data_row">
		<div class="data_label">Date of Birth:</div>
		<div class="data_value"><?php 
		$dobTime = strtotime($model->dob);
		echo date('jS F, Y', $dobTime);
		$age = floor((time() - $dobTime) / 60 / 60 / 24 / 365);
		echo ' (Age ' . $age  . ')';
		?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Gender:</div>
		<div class="data_value"><?php echo $model->gender == 'F' ? 'Female' : 'Male'; ?></div>
	</div>
</div>
<div class="details">
	<h3>Contact Details</h3>
	<div class="data_row">
		<div class="data_label">Telephone:</div>
		<div class="data_value"><?php echo $model->primary_phone; ?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Email:</div>
		<div class="data_value"> &nbsp; </div>
		
	</div>
	<div class="data_row">
		<div class="data_label">Next of Kin:</div>
		<div class="data_value"> &nbsp; </div>
	</div>
</div>
<div class="details">
	<h3>Recent Episodes</h3>
<?php
	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$episodes,
		'columns'=>array(
			array('name'=>'Start Date','value'=>'date("d/m/Y", strtotime($data->start_date))'),
			array('name'=>'End Date','value'=>'!empty($data->end_date) ? date("d/m/Y", strtotime($data->end_date)) : ""'),
			array('name'=>'Firm', 'value'=>'$data->firm->name'),
			array('name'=>'Service', 'value'=>'$data->firm->serviceSpecialtyAssignment->service->name'),
			array('name'=>'Eye','value'=>''), // 'diagnosis.location',
			array('name'=>'Diagnosis','value'=>''), // 'disorder.name'
			// @todo: figure out how to get this to switch to the episodes tab and select the correct episode to view
//			array('class'=>'CButtonColumn', 'buttons'=>array(
//				'view'=>array(
//					'label'=>'View',
//					'url'=>'"#"',
//					'click'=>'function() {console.log("test");$(".ui-tabs").tabs("select", 1); return false;}',
//					),
//				),
//				'template'=>'{view} View',
//			),
		),
		'enablePagination'=>false,
		'enableSorting'=>false,
		'summaryText'=>'',
		'emptyText'=>'No episodes found.'
	)); ?>
</div>
