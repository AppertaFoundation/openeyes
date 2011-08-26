<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all'); ?>
<h3>Operation Details</h3>
<div class="view">
	<strong><?php echo $data->getEyeLabelText(); ?></strong>
	<?php echo $data->getEyeText(); ?>
</div>
<div class="view">
	<table id="procedure_list" class="grid" title="Procedure List">
		<thead>
			<tr>
				<th>Procedures</th>
				<th>Duration</th>
			</tr>
		</thead>
		<tbody>
<?php
	$totalDuration = 0;
	if (!empty($data->procedures)) {
		foreach ($data->procedures as $procedure) {
			$totalDuration += $procedure->default_duration;
			$display = "{$procedure->term} - {$procedure['short_format']}"; ?>
			<tr>
				<td><?php echo $display; ?></td>
				<td><?php echo $procedure->default_duration; ?></td>
			</tr>
<?php	}
	} ?>
		</tbody>
		<tfoot>
			<tr>
				<td class="topPadded">Estimated Duration of Procedures:</td>
				<td id="projected_duration"><?php echo $totalDuration; ?></td>
			</tr>
			<tr>
				<td>Estimated Total:</td>
				<td><?php echo $data->total_duration; ?></td>
			</tr>
		</tfoot>
	</table>
</div>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('decision_date')); ?>:</b>
	<?php echo date('d/m/Y', strtotime($data->decision_date)); ?>
	<br />
</div>
<?php
	if ($data->consultant_required) { ?>
<div class="view">
	<strong>Consultant Required</strong>
	<br />
</div>
<?php
	} ?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('anaesthetic_type')); ?>:</b>
	<?php echo CHtml::encode($data->getAnaestheticText());

	if ($data->anaesthetist_required) { ?>
	<br />
	<strong>Anaesthetist Required</strong>
	<br />
<?php
	} ?>
</div>
<?php if (!empty($data->comments)) { ?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('comments')); ?>:</b>
	<?php echo nl2br(CHtml::encode($data->comments)); ?>
	<br />
</div>
<?php
	} ?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('overnight_stay')); ?>:</b>
	<?php echo CHtml::encode($data->getBooleanText('overnight_stay')); ?>
	<br />
</div>
<?php
	if (!empty($data->booking)) { ?>
<div class="view">
	<b><?php echo CHtml::encode('Ward'); ?>:</b>
	<?php echo CHtml::encode($data->booking->ward->name); ?>
	<br />
</div>
<?php
	}
	?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('schedule_timeframe')); ?>:</b>
	<?php echo CHtml::encode($data->getScheduleText()); ?>
	<br />
</div>
<?php
if ($data->status != ElementOperation::STATUS_CANCELLED && !empty($data->booking)) { ?>
<div class="view"><?php $this->renderPartial('/booking/_session',array('operation' => $data));
	$this->widget('zii.widgets.jui.CJuiAccordion', array(
		'panels'=>array(
			'Clinic details'=>$this->renderPartial('/booking/_clinic',
				array('operation' => $data),true),
		),
		'id'=>'clinic-details',
		// additional javascript options for the accordion plugin
		'options'=>array(
			'active'=>false,
			'animated'=>'bounceslide',
			'collapsible'=>true,
		),
	));
} ?>
</div>
<div class="cleartall"></div>
<?php
if ($data->schedule_timeframe != $data::SCHEDULE_IMMEDIATELY) {
	Yii::app()->user->setFlash('info',"Patient Request: Schedule On/After " . date('F j, Y', $data->getMinDate()));
} ?>
<script type="text/javascript">
	$('#procedure_list').show();
</script>