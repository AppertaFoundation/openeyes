<div class="admingroup curvybox">
	<h4>Profile</h4>
	<ul>
		<?php foreach (array(
			'Basic information' => '/profile/info',
			'Change password' => '/profile/password',
			'Sites' => '/profile/sites',
			'Firms' => '/profile/firms',
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
