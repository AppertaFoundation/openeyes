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
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?> class="active"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?>
					<span class="viewing"><?php echo $title?></span>
				<?php }else{?>
					<?php echo CHtml::link($title,array($uri))?>
				<?php }?>
			</li>
		<?php }?>
	</ul>
</div>
<div class="admingroup curvybox">
	<?php foreach (ModuleAdmin::getAll() as $module => $items) {?>
		<h4><?php echo $module?></h4>
		<ul>
			<?php foreach ($items as $item => $uri) {
				$e = explode('/',$uri);
				$action = array_pop($e)?>
				<li<?php if (Yii::app()->getController()->action->id == $action){?> class="active"<?php }?>>
					<?php if (Yii::app()->getController()->action->id == $action){?>
						<span class="viewing"><?php echo $item?></span>
					<?php }else{?>
						<?php echo CHtml::link($item,array($uri))?>
					<?php }?>
				</li>
			<?php }?>
		</ul>
	<?php }?>
</div>
