<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
	<h3>Adding New Event</h3>
	<p><strong>Select event to add:</strong></p>
	<?php foreach ($eventTypes as $eventType) {
		if ($eventType->class_name == 'OphTrOperation') {?>
			<p><a href="/clinical/create?event_type_id=25&patient_id=<?php echo $this->patient->id?>&firm_id=<?php echo $this->firm->id?>"><img src="/img/_elements/icons/event/small/treatment_operation.png" alt="operation" /> - <strong><?php echo $eventType->name ?></strong></a></p>
		<?php }else{
			$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.img').'/').'/';
			?>
			<p><a href="/<?php echo $eventType->class_name?>/default/create?patient_id=<?php echo $this->patient->id?>"><img src="<?php echo $assetpath?>small.png" alt="operation" /> - <strong><?php echo $eventType->name ?></strong></a></p>
		<?php }?>
	<?php }?>
</div>
