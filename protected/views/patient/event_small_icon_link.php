<?php
$event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/'; ?>
<a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
	<?php
	if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
		$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
	} else {
		$assetpath = '/assets/';
	}
	?>
	<img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op" width="19" height="19" />
</a>
