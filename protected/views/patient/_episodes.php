<div id="episodes_title">All Episodes</div>
<div id="add_episode">
	Click here to add an event to an episode
	<ul id="episode_types">
<?php
	foreach ($eventTypeGroups as $group => $eventTypes) { ?>
		<li class="header"><?php echo $group; ?></li>
<?php	foreach ($eventTypes as $type) { ?>
		<li><img src="/images/icon_<?php echo $type->name; ?>.png" alt="<?php 
		echo ucfirst($type->name); ?>" /><?php
			echo CHtml::link(
				ucfirst($type->name),
				Yii::app()->createUrl('clinical/create', array(
					'event_type_id' => $type->id
					))
				); ?></li>
<?php
		}
	} ?>
	</ul>
</div>
<div class="clear"></div>
<div id="episodes_sidebar">
<?php
	$this->renderPartial('/clinical/_episodeList', 
		array('episodes' => $episodes)
	); ?>
</div>
<div id="episodes_details">
	<?php 
	foreach ($episodes as $episode) {
		$this->renderPartial('/clinical/episodeSummary', 
			array('episode' => $episode)
		);
	} ?>
</div>
<script type="text/javascript">
	$('#add_episode').hover(
		function() {
			$('#episode_types').slideDown({'duration':75});
		},
		function() {
			$('#episode_types').hide();
	});
	$('#episode_types li[class!=header]').click(function() {
		$.ajax({
			url: $(this).children('a').attr('href'),
			success: function(data) {
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
</script>