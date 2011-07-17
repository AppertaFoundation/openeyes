<?php
if (empty($episodes)) {
	echo 'No episodes.';
} else {
	foreach ($episodes as $episode) { ?>
<div class="episode">
	<div class="title">
		<input type="hidden" name="episode-id" value="<?php echo $episode->id; ?>" />
		<span class="date"><?php echo date('d/m/y', strtotime($episode->start_date)); ?></span> - <?php
		echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name); ?></div>
	<ul class="events">
<?php
		foreach ($episode->events as $event) { ?>
		<li><?php
		$text = '<span class="type">' . ucfirst($event->eventType->name) . 
			'</span><span class="date"> - ' . date('d/m/Y', strtotime($event->datetime)) .
			'</span>';
		echo CHtml::link($text, array('clinical/view', 'id'=>$event->id));
		} ?>
	</ul>
	<div class="footer"></div>
</div>
<?php
	}
}
?>
