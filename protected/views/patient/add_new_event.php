<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
	<h3>Adding New Event</h3>
	<p><strong>Select event to add:</strong></p>
	<?php foreach ($eventTypes as $eventType) {
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
	<?php }?>
</div>
