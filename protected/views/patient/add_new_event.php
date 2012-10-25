<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
	<h3>Adding New Event</h3>
	<p><strong>Select event to add:</strong></p>
	<?php foreach ($eventTypes as $eventType) {
		if (!is_array(Yii::app()->params['modules_disabled']) || !isset(Yii::app()->params['modules_disabled'][$eventType->class_name])) {
			if ($eventType->class_name == 'OphTrOperation') {?>
				<p><?php echo CHtml::link('<img src="'.Yii::app()->createUrl('img/_elements/icons/event/small/treatment_operation.png').'" alt="operation" /> - <strong>'.$eventType->name.'</strong>',Yii::app()->createUrl('clinical/create').'?event_type_id=25&patient_id='.$this->patient->id.'&firm_id='.$this->firm->id)?></p>
			<?php }else{
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
					$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
				} else {
					$assetpath = '/assets/';
				}
				?>
				<p><?php echo CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>',Yii::app()->createUrl($eventType->class_name.'/Default/create').'?patient_id='.$this->patient->id)?></p>
			<?php }?>
		<?php }else{
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
					$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
				} else {
					$assetpath = '/assets/';
				}
				?>
				<p id="<?php echo $eventType->class_name?>_disabled" class="add_event_disabled" rel="<?php echo Yii::app()->params['modules_disabled'][$eventType->class_name]?>">
					<?php echo CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>','#')?>
				</p>
			<?php }?>
	<?php }?>
</div>
<div id="add-event-disabled-pop-up">
	<h3>This module is disabled</h3>
	<p id="add-event-disabled-pop-up-text">
		This div only appears when the trigger link is hovered over. Otherwise
		it is hidden from view.
	</p>
</div>
<script type="text/javascript">
	$('#OphCiExamination_disabled').hover(function(e) {
		$('#add-event-disabled-pop-up-text').text($(this).attr('rel'));
		$('#add-event-disabled-pop-up').show();

	}, function() {
		$('#add-event-disabled-pop-up').hide();
	});

	$('#OphCiExamination_disabled').mousemove(function(e) {
		var moveLeft = -100;
		var moveDown = -150;
		$("#add-event-disabled-pop-up").css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
	});
</script>
