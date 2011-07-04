<?php

foreach ($episodes as $episode) { ?>
<div class="episode rounded-corners">
	<span class="title">
		<input type="hidden" name="episode-id" value="<?php echo $episode->id; ?>" />
		<span class="date"><?php echo date('d M Y', strtotime($episode->start_date));
	echo '</span> - ';
	echo $episode->firm->serviceSpecialtyAssignment->specialty->name; ?></span>
<ul class="events">
<?php
	foreach ($episode->events as $event) { ?>
	<li><?php 
	$text = '<span class="type">' . ucfirst($event->eventType->name) . 
		'</span> - <span class="date">' . date('d/m/Y', strtotime($event->datetime)) . 
		'</span>';
	echo CHtml::link($text, array('clinical/view', 'id'=>$event->id));
	} ?>
</ul>
</div>
<?php
}
?>
