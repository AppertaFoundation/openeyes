<h3>Operation Details</h3>
<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;

Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
?>
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
				<td>Estimated Duration of Procedures:</td>
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
        <?php echo CHtml::encode($data->decision_date); ?>
        <br />
</div>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('consultant_required')); ?>:</b>
	<?php echo CHtml::encode($data->getBooleanText('consultant_required')); ?>
	<br />
</div>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('anaesthetic_type')); ?>:</b>
	<?php echo CHtml::encode($data->getAnaestheticText()); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('anaesthetist_required')); ?>:</b>
	<?php echo CHtml::encode($data->getBooleanText('anaesthetist_required')); ?>
	<br />
</div>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('comments')); ?>:</b>
	<?php echo nl2br(CHtml::encode($data->comments)); ?>
	<br />
</div>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('overnight_stay')); ?>:</b>
	<?php echo CHtml::encode($data->getBooleanText('overnight_stay')); ?>
	<br />
</div>
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
		'themeUrl'=>Yii::app()->baseUrl . '/css/jqueryui',
		'theme'=>'theme',
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
}
