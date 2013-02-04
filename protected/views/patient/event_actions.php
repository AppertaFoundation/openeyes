<div class="event_actions_sticky_wrapper">
	<div class="event_actions stuck clearfix">
		<ul>
				<?php foreach($this->event_actions as $action) { ?>
					 <li><?php echo $action->toHtml();?></li>
				<?php } ?>
		</ul>
	</div>
</div>
