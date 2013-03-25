<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
	<h3>Adding New Event</h3>
	<p><strong>Select event to add:</strong></p>
	<?php foreach ($eventTypes as $eventType) {
		if (!$eventType->disabled && $this->checkEventAccess($eventType)) {
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
		<?php } else {
			if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
				$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
			} else {
				$assetpath = '/assets/';
			}
			?>
			<?php if($eventType->disabled) { ?>
			<p id="<?php echo $eventType->class_name?>_disabled" class="add_event_disabled" data-title="<?php echo $eventType->disabled_title?>" data-detail="<?php echo $eventType->disabled_detail?>">
				<?php echo CHtml::link('<img src="'.$assetpath.'small.png" /> - <strong>'.$eventType->name.'</strong>','#')?>
			</p>
			<?php } ?>
		<?php }?>
	<?php }?>
</div>
<div id="add-event-disabled-pop-up">
	<h3 id="add-event-disabled-pop-up-title"></h3>
	<p id="add-event-disabled-pop-up-detail">
	</p>
</div>
<script type="text/javascript">
	$('p.add_event_disabled').hover(function(e) {
		$('#add-event-disabled-pop-up-title').text($(this).attr('data-title'));
		$('#add-event-disabled-pop-up-detail').text($(this).attr('data-detail'));
		$('#add-event-disabled-pop-up').show();

	}, function() {
		$('#add-event-disabled-pop-up').hide();
	});
</script>
