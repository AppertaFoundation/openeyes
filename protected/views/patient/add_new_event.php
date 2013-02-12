<div id="add_event_wrapper">
	<button tabindex="2" class="classy venti green" id="addNewEvent" type="submit" style="float: right; margin-right: 1px;"><span class="button-span button-span-green with-plussign">add new Event</span></button>
	<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
		<h3>Adding New Event</h3>
		<p><strong>Select event to add:</strong></p>
		<?php foreach ($eventTypes as $eventType) {
			if (!$eventType->disabled) {
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
					$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
				} else {
					$assetpath = '/assets/';
				}
				?>
				<p><?php echo CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>',Yii::app()->createUrl($eventType->class_name.'/Default/create').'?patient_id='.$this->patient->id)?></p>
			<?php }else{
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
					$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
				} else {
					$assetpath = '/assets/';
				}
				?>
				<p id="<?php echo $eventType->class_name?>_disabled" class="add_event_disabled" data-title="<?php echo $eventType->disabled_title?>" data-detail="<?php echo $eventType->disabled_detail?>">
					<?php echo CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>','#')?>
				</p>
			<?php }?>
		<?php }?>
		<div id="add-event-disabled-pop-up">
			<h3 id="add-event-disabled-pop-up-title"></h3>
			<p id="add-event-disabled-pop-up-detail"></p>
		</div>
	</div>
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
