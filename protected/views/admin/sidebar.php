<div class="admingroup curvybox">
	<h4>Core</h4>
	<ul>
		<?php foreach (array(
			'Users' => '/admin/users',
			'Firms' => '/admin/firms',
			'Global phrases' => '/admin/globalPhrases',
			'Phrases by subspecialty' => '/admin/phrasesBySubspecialty',
			'Phrases by firm' => '/admin/phrasesByFirm',
			'Letter templates' => '/admin/letterTemplates',
			'Sequences' => '/admin/sequences',
			'Sessions' => '/admin/sessions',
			'Episode status' => '/admin/episodeStatus',
		) as $title => $uri) {?>
			<li<?php if (false) {?> class="active"<?php }?>>
				<?php echo CHtml::link($title,array($uri))?>
			</li>
		<?php }?>
	</ul>
</div>
<div class="admingroup curvybox">
	<?php foreach (ModuleAdmin::getAll() as $module => $items) {?>
		<h4><?php echo $module?></h4>
		<ul>
			<?php foreach ($items as $item => $uri) {?>
				<li>
					<?php echo CHtml::link($item,array($uri))?>
				</li>
			<?php }?>
		</ul>
	<?php }?>
</div>
