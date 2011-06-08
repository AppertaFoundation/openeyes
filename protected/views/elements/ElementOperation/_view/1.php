<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all'); ?>
<strong>Operation Details</strong>
<div class="view">
	<strong><?php echo $data->getAttributeLabel('eye'); ?></strong>
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
<div class="view">
	<?php echo CHtml::link("Schedule Now",
		array('appointment/schedule', 'operation'=>$data->id), array('id'=>'inline', 'encode'=>false)); ?>
</div>
<?php
if ($data->schedule_timeframe != $data::SCHEDULE_IMMEDIATELY) {
	Yii::app()->user->setFlash('info',"Patient Request: Schedule On/After " . date('F j, Y', $data->getMinDate()));
}
$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'a#inline',
    'config'=>array(),
    )
); ?>
<script type="text/javascript">
	$('#cancel').live('click', function() {
		$.fancybox.close();
	});
</script>