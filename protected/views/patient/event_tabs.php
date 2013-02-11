<div class="event_tabs clearfix">
	<ul>
		<?php foreach($this->event_tabs as $tab) { ?>
		<li<?php if(@$tab['active']) { ?> class="active"<?php } ?>>
			<?php if(@$tab['href']) { ?>
			<a href="<?php echo $tab['href'] ?>"><span><?php echo $tab['label'] ?></span></a>
			<?php } else { ?>
			<span><?php echo $tab['label'] ?></span>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
</div>
