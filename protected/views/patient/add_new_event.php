<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
	<h3>Adding New Event</h3>
	<p><strong>Select event to add:</strong></p>
	<?php foreach ($eventTypes as $eventType) {
		if ($eventType->class_name == 'OphTrOperation') {?>
			<p><a href="/clinical/create?event_type_id=25&patient_id=<?php echo $patient->id?>&firm_id=<?php echo $this->firm->id?>"><img src="/img/_elements/icons/event/small/treatment_operation_unscheduled.png" alt="operation" width="16" height="16" /> - <strong><?php echo $eventType->name ?></strong></a></p>
		<?}else{?>
			<p><a href="/<?php echo $eventType->class_name?>/Default/create?patient_id=<?php echo $patient->id?>"><img src="/img/_elements/icons/event/small/treatment_operation_unscheduled.png" alt="operation" width="16" height="16" /> - <strong><?php echo $eventType->name ?></strong></a></p>
		<?}?>
	<?php }?>
</div>
