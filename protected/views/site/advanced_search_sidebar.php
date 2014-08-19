<div class="box admin">
	<h2>Core</h2>
	<ul class="navigation admin">
	</ul>
</div>
<?php foreach (Yii::app()->params['advanced_search'] as $module => $pages) {
	if ($et = EventType::model()->find('class_name=?',array($module))) {
		$name = $et->name;
	} else {
		$name = $module;
	}?>
	<div class="box admin">
		<h2><?php echo $name?></h2>
		<ul class="navigation admin">
			<?php foreach ($pages as $title => $uri) {?>
				<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?> class="selected"<?php }?>>
					<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?>
						<?php echo CHtml::link($title,Yii::app()->createUrl('/'.$module.'/search/'.$uri),array('class' => 'selected'))?>
					<?php } else {?>
						<?php echo CHtml::link($title,Yii::app()->createUrl('/'.$module.'/search/'.$uri))?>
					<?php }?>
				</li>
			<?php }?>
		</ul>
	</div>
<?php }?>
