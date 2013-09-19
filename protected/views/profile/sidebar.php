<div class="admingroup curvybox">
	<h4>Profile</h4>
	<ul>
		<?php
		$links = array();
		if (Yii::app()->params['profile_user_can_edit']) {
			$links['Basic information'] = '/profile/info';
		}
		if (Yii::app()->params['profile_user_can_change_password']) {
			$links['Change password'] = '/profile/password';
		}
		foreach (array_merge($links,array(
			'Sites' => '/profile/sites',
			'Firms' => '/profile/firms',
		)) as $title => $uri) {?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?> class="active"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?>
					<span class="viewing"><?php echo $title?></span>
				<?php } else {?>
					<?php echo CHtml::link($title,array($uri))?>
				<?php }?>
			</li>
		<?php }?>
	</ul>
</div>
