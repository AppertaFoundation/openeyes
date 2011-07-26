<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all'); ?>
<h3>Operation Details</h3>
<?php
if ($data->status == $data::STATUS_CANCELLED) { ?>
<div class="flash-error">
<?php 
	$cancellation = $data->cancellation;
	// todo: move this to a nicer place
	$text = "Operation Cancelled: By " . $cancellation->user->first_name . 
		' ' . $cancellation->user->last_name . ' on ' . date('F j, Y', strtotime($cancellation->cancelled_date));
	$text .= ' [' . $cancellation->cancelledReason->text . ']';	
	
	echo $text; ?>
</div>
<?php 
} ?>
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
if ($data->status != ElementOperation::STATUS_CANCELLED && !empty($data->booking)) {
	$this->renderPartial('/booking/_session',array('operation' => $data));
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
<div class="buttonlist">
<?php
if ($data->status != ElementOperation::STATUS_CANCELLED) {
	if (empty($data->booking)) {
		echo CHtml::link('<span>Cancel Operation</span>',
			array('booking/cancelOperation', 'operation'=>$data->id), array('class'=>'fancybox shinybutton', 'encode'=>false));
		echo CHtml::link("<span>Schedule Now</span>",
			array('booking/schedule', 'operation'=>$data->id), array('class'=>'fancybox shinybutton highlighted', 'encode'=>false));
//	$confirmJs = "js:function() {
//			$('#confirm').live('click', function() {
//				var operation = $('input[id=operation]').val();
//				var session = $('input[name=session_id]').val();
//				$.post('" . Yii::app()->createUrl('booking/create') . "', {
//					'Booking': {
//						'element_operation_id': operation,
//						'session_id': session
//					}
//				});
//				//$.fancybox.close();
//				//parent.$.fancybox.close();
//			});
//		}";
	} else {
		echo '<p/>';
		echo CHtml::link('<span>Cancel Operation</span>',
			array('booking/cancelOperation', 'operation'=>$data->id), array('class'=>'fancybox shinybutton', 'encode'=>false));
		echo CHtml::link("<span>Re-Schedule Later</span>",
			array('booking/rescheduleLater', 'operation'=>$data->id), array('class'=>'fancybox shinybutton', 'encode'=>false));
		echo CHtml::link('<span>Re-Schedule Now</span>',
			array('booking/reschedule', 'operation'=>$data->id), array('class'=>'fancybox shinybutton highlighted', 'encode'=>false));
		echo '<p/>';
	
//	$confirmJs = "js:function() {
//			$('#confirm').live('click', function() {
//				var booking = $('input[id=booking]').val();
//				var operation = $('input[id=operation]').val();
//				var session = $('input[name=session_id]').val();Ω
//				$.post('" . Yii::app()->createUrl('booking/update') . "', {
//					'Booking': {
//						'id': booking,
//						'element_operation_id': operation,
//						'session_id': session
//					},
//					'cancellation_reason': $('select[name=cancellation_reason]').val()
//				});
//				//parent.$.fancybox.close(); 
//			});
//		}";
	}
}?>
</div>
<div class="clear"><p/></div>
<?php
if ($data->schedule_timeframe != $data::SCHEDULE_IMMEDIATELY) {
	Yii::app()->user->setFlash('info',"Patient Request: Schedule On/After " . date('F j, Y', $data->getMinDate()));
}
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.fancybox',
	'config'=>array()
	)
);
?>