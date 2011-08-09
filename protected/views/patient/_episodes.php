<div id="box_gradient_top"></div>
<div id="box_gradient_bottom">
<h3>All Episodes</h3>
<div id="add_episode">
	<img src="/images/add_event_button.png" alt="Add an event to this episode" />
	<ul id="episode_types">
<?php
	foreach ($eventTypeGroups as $group => $eventTypes) { ?>
		<li class="header"><?php echo $group; ?></li>
<?php	foreach ($eventTypes as $type) {
			$name = ucfirst($type->name); ?>
		<li><img src="/images/icon_<?php echo $type->name; ?>.png" alt="<?php 
		echo $name; ?>" /><?php
		echo CHtml::link($name, array('clinical/create', 'event_type_id'=>$type->id), 
			array('class'=>'fancybox2', 'encode'=>false)); ?></li>
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

	// View the open episode for this firm's specialty, if any
	foreach ($episodes as $ep) {
		if ($ep->firm->serviceSpecialtyAssignment->specialty_id == $firm->serviceSpecialtyAssignment->specialty_id) {
			// @todo - change to give priority to the open episode for the specialty
			$episode = $ep;
		}
	}

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
	$('#episode_types li a').click(function() {
		$('ul.events li.shown').removeClass('shown');
	});
	$('ul.events li a').live('click', function() {
		$('ul.events li.shown').removeClass('shown');
		$(this).parent().addClass('shown');
		$.ajax({
			url: $(this).attr('href'),
			success: function(data) {
				$('#episodes_details').show();
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
	$('.episode div.title').live('click', function() {
		var id = $(this).children('input').val();
		$('ul.events li.shown').removeClass('shown');
		$.ajax({
			url: '<?php echo Yii::app()->createUrl('clinical/episodeSummary'); ?>',
			type: 'GET',
			data: {'id': id},
			success: function(data) {
				$('#episodes_details').show();
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
	$('a.fancybox2').fancybox({'onStart':function() { $('ul.events li.shown').removeClass('shown'); }});
</script>
