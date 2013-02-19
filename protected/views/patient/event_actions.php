<div class="event_actions clearfix">
	<ul>
		<li><img class="loader" style="display: none;" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." /></li>
		<?php foreach($this->event_actions as $action) { ?>
		<li><?php echo $action->toHtml();?></li>
		<?php } ?>
	</ul>
</div>
