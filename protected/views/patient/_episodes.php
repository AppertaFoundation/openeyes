<div id="box_gradient_top"></div>
<div id="box_gradient_bottom">
<h3>All Episodes</h3>
<div id="add_episode">
	<img src="/images/add_event_button.png" alt="Add an event to this episode" />
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
	$episode = end($episodes);
	$this->renderPartial('/clinical/episodeSummary',
		array('episode' => $episode)
	); ?>
</div>
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
	$('ul.events li a').live('click', function() {
		$.ajax({
			url: $(this).attr('href'),
			success: function(data) {
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
	$('.episode div.title').live('click', function() {
		var id = $(this).children('input').val();
		$.ajax({
			url: '<?php echo Yii::app()->createUrl('clinical/episodeSummary'); ?>',
			type: 'GET',
			data: {'id': id},
			success: function(data) {
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
</script>