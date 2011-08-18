<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false; ?>
<div id="box_gradient_top"></div>
<div id="box_gradient_bottom">
<h3>Summary</h3>
<div class="details" id="personal_details">
	<h3>Personal Details:</h3>
	<div class="data_row">
		<div class="data_label">First name(s):</div>
		<div class="data_value"><?php echo $model->first_name; ?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Last name:</div>
		<div class="data_value"><?php echo $model->last_name; ?></div>
	</div>
	<div class="data_row address">
		<div class="data_label">Address:</div>
		<div class="data_value">
<?php 
	if (!empty($address)) {
		$fields = $address->getAttributes();
		unset($fields['id'], $fields['country_id']);
		$addressList = array_filter($fields, 'filter_nulls');

		foreach ($addressList as $name => $string) {
			if ($name === 'postcode') {
				$string = strtoupper($string);
			}
			if ($name != 'email') {
				echo $string;
			}
			if ($string != end($addressList)) {
				echo '<br />';
			}
		}
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
		echo ' (Age ' . $model->getAge()  . ')';
		?></div>
	</div>
	<div class="data_row row_buffer">
		<div class="data_label">Gender:</div>
		<div class="data_value"><?php echo $model->gender == 'F' ? 'Female' : 'Male'; ?></div>
	</div>
</div>
<div class="details" id="contact_details">
	<h3>Contact Details:</h3>
	<div class="data_row telephone">
		<div class="data_label">Telephone:</div>
		<div class="data_value"><?php echo !empty($model->primary_phone) 
			? $model->primary_phone : 'Unknown'; ?></div>
	</div>
	<div class="data_row row_buffer">
		<div class="data_label">Email:</div>
		<div class="data_value"><?php echo !empty($address->email) 
                        ? $address->email : 'Unknown'; ?></div>
		
	</div>
	<div class="data_row row_buffer">
		<div class="data_label">Next of Kin:</div>
		<div class="data_value">Unknown</div>
	</div>
</div>
<div class="details" id="recent_episodes">
	<h3>Recent Episodes:</h3>
	<div id="view_all"></div>
	<div class="clear"></div>
<?php
	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$episodes,
		'columns'=>array(
			array('name'=>'Start Date','value'=>'date("d/m/Y", strtotime($data->start_date))'),
			array('name'=>'End Date','value'=>'!empty($data->end_date) ? date("d/m/Y", strtotime($data->end_date)) : ""'),
			array('name'=>'Firm', 'value'=>'$data->firm->name'),
			array('name'=>'Service', 'value'=>'$data->firm->serviceSpecialtyAssignment->service->name'),
			array('name'=>'Eye','value'=>'$data->getPrincipalDiagnosisEyeText()'), // 'diagnosis.location',
			array('name'=>'Diagnosis','value'=>'$data->getPrincipalDiagnosisDisorderTerm()'), // 'disorder.name'
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
</div>
<script type="text/javascript">
	$('#view_all').live('click', function() {
		$('#patient-tabs').tabs('select', 1);
	});
</script>
<?php
function filter_nulls($data) {
	return $data !== null;
}
